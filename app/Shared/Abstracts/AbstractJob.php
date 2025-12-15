<?php

declare(strict_types=1);

namespace App\Shared\Abstracts;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

/**
 * Базовый абстрактный класс для асинхронных заданий (Job),
 * использующих в качестве очереди RabbitMQ.
 *
 * Классы-наследники должны реализовать методы:
 * - {@see handle()} — основная логика выполнения задания;
 * - {@see failed()} — обработка ошибок выполнения.
 */
abstract class AbstractJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Количество секунд, в течение которых задание может выполняться до тайм-аута.
     */
    public int $timeout;

    /**
     * @param int      $tries   количество попыток выполнения задания
     * @param int      $backoff количество секунд между попытками
     * @param int|null $timeout максимальный тайм-аут задания
     */
    public function __construct(
        public int $tries = 3,
        public int $backoff = 10,
        ?int       $timeout = null
    ) {
        /** @var string|null $queueName */
        $queueName = Config::get('queue.connections.rabbitmq.queue');
        if (is_string($queueName)) {
            $this->onQueue($queueName);
        }

        /** @var int|null $defaultTimeout */
        $defaultTimeout = Config::get('queue.connections.rabbitmq.timeout');
        $this->timeout  = is_int($defaultTimeout) ? $defaultTimeout : 60;

        if ($timeout !== null && $timeout > $this->timeout) {
            $this->timeout = $timeout;
        }
    }
}

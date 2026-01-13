<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Modules\Telegram\Interfaces\Services\TelegramBotServiceInterface;
use Illuminate\Console\Command;
use Throwable;

/**
 * Консольная команда для установки webhook Telegram-бота
 */
final class SetTelegramWebhook extends Command
{
    protected $signature = 'telegram:set-webhook {--url= : Пользовательский URL webhook (необязательно)}';

    protected $description = 'Установить URL webhook для Telegram-бота';

    public function handle(TelegramBotServiceInterface $botService): int
    {
        try {
            $webhookUrl = $this->option('url') ?? config('services.telegram.webhook_url');

            if (!is_string($webhookUrl) || $webhookUrl === '' || $webhookUrl === '0') {
                $this->error(
                    'URL webhook не задан. Укажите TELEGRAM_WEBHOOK_URL в файле .env или используйте опцию --url'
                );

                return self::FAILURE;
            }

            $this->info('Установка webhook по адресу: ' . $webhookUrl);

            // Устанавливаем webhook в Telegram
            $botService->setWebhook($webhookUrl);

            $this->info('Webhook успешно установлен!');

            // Получаем информацию о webhook для проверки
            $this->info('Получение информации о webhook...');
            $webhookInfo = $botService->getWebhookInfo();

            $this->newLine();
            $this->info('Информация о webhook:');
            $this->table(
                ['Параметр', 'Значение'],
                [
                    ['URL', $webhookInfo->getUrl()],
                    ['Наличие пользовательского сертификата', $webhookInfo->hasCustomCertificate() ? 'Да' : 'Нет'],
                    ['Количество ожидающих обновлений', $webhookInfo->getPendingUpdateCount()],
                    ['Дата последней ошибки', $webhookInfo->getLastErrorDate()],
                    ['Сообщение последней ошибки', $webhookInfo->getLastErrorMessage()],
                ]
            );

            return self::SUCCESS;
        } catch (Throwable $throwable) {
            $this->error('Не удалось установить webhook: ' . $throwable->getMessage());

            return self::FAILURE;
        }
    }
}

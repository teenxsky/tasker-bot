<?php

declare(strict_types=1);

namespace App\Shared\Abstracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Этот класс создан для того, чтобы обеспечить абстракцию,
 * упрощающую переход с одного фреймворка на другой в будущем.
 * Он служит базовым классом для всех сущностей, предоставляя
 * единый интерфейс и минимизируя зависимость от конкретного фреймворка.
 *
 * @template T of AbstractModel
 */
abstract class AbstractModel extends Model
{
}

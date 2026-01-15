<?php

declare(strict_types=1);

namespace Modules\FormBuilder;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Modules\FormBuilder\Contracts\Input as InputContract;
use Modules\FormBuilder\Enums\InputType;
use Modules\FormBuilder\View\Components\Checkbox;
use Modules\FormBuilder\View\Components\FileUploader;
use Modules\FormBuilder\View\Components\Input;
use Modules\FormBuilder\View\Components\Radio;
use Modules\FormBuilder\View\Components\RichEditor;
use Modules\FormBuilder\View\Components\Select;

class Schema
{
    protected array $inputViews = [];

    protected ?string $title = null;

    protected ?string $description = null;

    protected ?Collection $formSchemas = null;

    public function __construct()
    {
        //
    }

    public static function configure(self|callable $schema): static
    {
        if (is_callable($schema)) {
            return $schema(new static);
        }

        return $schema;
    }

    public function header(
        string|callable|null $title = null,
        string|callable|null $description = null,
    ): self {
        return $this->title($title)->description($description);
    }

    public function title(string|callable|null $title = null): self
    {
        if (is_callable($title) && is_string($title())) {
            $this->title = $title();
        }

        $this->title = $title;

        return $this;
    }

    public function description(string|callable|null $description = null): self
    {
        if (is_callable($description) && is_string($description())) {
            $this->description = $description();
        }

        $this->description = $description;

        return $this;
    }

    /**
     * @param array<int, \Modules\FormBuilder\Contracts\Input> $schemas
     */
    public function form(array $schemas = []): self
    {
        $this->formSchemas = $this->mapFormSchemas($schemas);

        return $this;
    }

    public function getFormAttributes(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'schemas' => $this->formSchemas,
            'inputViews' => $this->inputViews,
        ];
    }

    /**
     * @param array<string, class-string> $viewClasses
     */
    public function registerInputViews(array $viewClasses = []): void
    {
        $this->inputViews = array_merge($this->getDefaultInputViews(), $viewClasses);
    }

    public function getDefaultInputViews(): array
    {
        return [
            InputType::INPUT->value => Input::class,
            InputType::SELECT->value => Select::class,
            InputType::RADIO->value => Radio::class,
            InputType::CHECKBOX->value => Checkbox::class,
            InputType::FILE->value => FileUploader::class,
            InputType::EDITOR->value => RichEditor::class,
        ];
    }

    protected function mapFormSchemas(array $schemas = []): Collection
    {
        if (empty($schemas)) {
            return collect();
        }

        return collect($schemas)->map(function ($schema) {
            if (! ($schema instanceof InputContract)) {
                throw new InvalidArgumentException(
                    sprintf('The schema must be instance of %s.', InputContract::class),
                );
            }

            return $schema;
        });
    }
}

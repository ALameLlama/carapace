# GroupFrom

GroupFrom allows you to collect multiple input keys into a single array property during pre-hydration. This is helpful when inputs are spread across different keys but you want them grouped in your DTO.

## Usage

```php
<?php

declare(strict_types=1);

use Alamellama\Carapace\Attributes\GroupFrom;
use Alamellama\Carapace\Data;

class Report extends Data
{
    public function __construct(
        #[GroupFrom(['q1', 'q2', 'q3', 'q4'])]
        public array $quarters,
    ) {}
}

$report = Report::from([
    'q1' => 10,
    'q2' => 12,
    'q3' => 9,
    'q4' => 14,
]);

// $report->quarters === [10, 12, 9, 14]
```

You can also combine with MapFrom to rename incoming keys before grouping.

## Notes

- Missing keys are ignored by default; use nulls if needed and cast later.
- Runs during pre-hydration phase.

<?php

declare(strict_types=1);

namespace App\Presenters;

use DateTi\DateTi;
use Nette;
use stdClass;

/**
 * @property-read Nette\Bridges\ApplicationLatte\Template|stdClass $template
 */
class HomepagePresenter extends Nette\Application\UI\Presenter
{

    /** @var DateTi */
    private $dateTi;

    public function __construct(DateTi $dateTi)
    {
        $this->dateTi = $dateTi;
    }

    public function renderDefault(): void
    {
        $this->template->date = $this->dateTi->format('d.m.Y');

    }
}

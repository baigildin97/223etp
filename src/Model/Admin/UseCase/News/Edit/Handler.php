<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\News\Edit;


use App\Model\Admin\Entity\News\Id;
use App\Model\Admin\Entity\News\News;
use App\Model\Admin\Entity\News\NewsRepository;
use App\Model\Flusher;

/**
 * Class Handler
 * @package App\Model\Admin\UseCase\News\Create
 */
class Handler
{

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var NewsRepository
     */
    private $newsRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param NewsRepository $newsRepository
     */
    public function __construct(Flusher $flusher, NewsRepository $newsRepository)
    {
        $this->flusher = $flusher;
        $this->newsRepository = $newsRepository;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command){
        $news = $this->newsRepository->get(new Id($command->id));
        $news->edit($command->subject, $command->text);
        $this->flusher->flush();
    }
}
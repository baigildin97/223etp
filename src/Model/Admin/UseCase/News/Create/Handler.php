<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\News\Create;


use App\Model\Admin\Entity\News\Id;
use App\Model\Admin\Entity\News\News;
use App\Model\Admin\Entity\News\NewsRepository;
use App\Model\Admin\Entity\News\Status;
use App\Model\Flusher;
use Exception;

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
     * @throws Exception
     */
    public function handle(Command $command){

        if ($command->delayedPublication  === null) {
            $delayedPublication = new \DateTimeImmutable();
        }else{
            $delayedPublication = new \DateTimeImmutable($command->delayedPublication);
        }

        $news = new News(
          Id::next(),
          Status::active(),
          $command->subject,
          $command->text,
          $delayedPublication,
          new \DateTimeImmutable()
        );

        $this->newsRepository->add($news);

        $this->flusher->flush();

    }
}
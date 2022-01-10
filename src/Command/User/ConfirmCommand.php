<?php
declare(strict_types=1);
namespace App\Command\User;

use App\Model\User\UseCase\User\SignUp\Confirm\Manual\Handler;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ConfirmCommand extends Command
{
    private $user;
    private $handler;

    public function __construct(UserFetcher $fetcher, Handler $handler)
    {
        $this->user = $fetcher;
        $this->handler = $handler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('user:confirm')->setDescription('Confirms signed up user');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $email = $helper->ask($input, $output, new Question('Email: '));

        if (!$user = $this->user->findByEmail($email)){
            throw new LogicException('User is not found.');
        }

        $command = new \App\Model\User\UseCase\SignUp\Confirm\Manual\Command($user->id);

        $this->handler->handle($command);
        $output->writeln('<info>Done!</info>');
    }
}
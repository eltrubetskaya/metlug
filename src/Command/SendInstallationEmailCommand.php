<?php

namespace App\Command;


use App\Entity\Installation;
use App\Entity\User;
use App\Service\MailService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\RouterInterface;

class SendInstallationEmailCommand extends Command
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var MailService
     */
    private $mailService;

    public function __construct(
        RouterInterface $router,
        ManagerRegistry $doctrine,
        MailService $mailService
    ) {
        $this->router = $router;
        $this->doctrine = $doctrine;
        $this->mailService = $mailService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:send-email:create-installation')
            ->setDescription('Send Emails')
            ->addArgument('title', InputArgument::REQUIRED, 'Installation title')
            ->addArgument('email', InputArgument::OPTIONAL, 'email');
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $title = $input->getArgument('title');

        /** @var Installation $installation */
        $installation = $this->doctrine->getRepository(Installation::class)->findOneBy([
            'title' => $title
        ]);
        if ($installation) {
            $io = new SymfonyStyle($input, $output);
            if ($email) {
                try {
                    $this->mailService->sendMail($email, $installation->getDescription(), MailService::CREATE_INSTALLATION_TEMPLATE, [
                        'title' => $installation->getDescription(),
                        'link' => $this->router->generate('installation_show', [
                            'title' => $title
                        ])
                    ]);
                } catch (\Exception $exception) {

                    $io->error('Error: ' . $exception->getMessage());

                    return 0;
                }
                $io->success('Email was sent.');

                return 0;
            }
            $users = $this->doctrine->getRepository(User::class)->findAll();
            foreach ($users as $user) {
                try {
                    $this->mailService->sendMail($user->getEmail(), $installation->getDescription(), MailService::CREATE_INSTALLATION_TEMPLATE . $title, [
                        'title' => $installation->getDescription(),
                        'link' => $this->router->generate('installation_show', [
                            'title' => $title
                        ])
                    ]);
                } catch (\Exception $exception) {

                    $io->error('Error: ' . $exception->getMessage());
                }
            }
            $io->success('Email was sent.');
        }
        $io->error('Installation not found ' . $title);

        return 0;
    }
}

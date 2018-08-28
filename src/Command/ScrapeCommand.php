<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ScrapeCommand extends Command
{
    private $baseURI = 'https://infopemilu.kpu.go.id/pilkada2018/pemilih/dpt/1/BANTEN/';
    private $suffix = 'listDps.json';
    private $timestamp;
    protected static $defaultName = 'app:scrape';

    protected function configure()
    {
        $this
            ->setDescription('Scrap daftar pemilih tetap pilkada tahun 2018 di Provinsi Banten.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Memulai scrapping data ...');
        $io->success('Proses scrapping telah selesai.');
    }
}

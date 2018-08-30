<?php

namespace App\Command;

use App\Entity\Pemilih;
use App\Entity\Target;
use App\Repository\TargetRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ScrapeCommand extends Command
{
    private $baseURI = 'https://infopemilu.kpu.go.id/pilkada2018/pemilih/dpt/1/BANTEN/';
    private $suffix = 'listDps.json';
    private $timestamp;
    private $em;
    private $provinsi;
    private $kota;
    private $kecamatan;
    private $kelurahan;
    private $tps;
    protected static $defaultName = 'app:scrape';

    public function __construct(EntityManagerInterface $em, TargetRepository $targetRepository)
    {
        parent::__construct();

        $this->em = $em;
        $this->targetRepository = $targetRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Scrap daftar pemilih tetap pilkada tahun 2018 di Provinsi Banten.')
            ->addArgument('type', InputArgument::REQUIRED, 'Which process to start, producer or worker. Valid value is \'producer\' and \'worker\'')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Memulai scrapping data ...');
        $processType = $input->getArgument('type');
        switch ($processType) {
            case 'producer':
                $this->startProducer($io);
                break;
            case 'worker':
                $this->startWorker($io, $output);
                break;
        }
        $io->success('Proses scrapping telah selesai.');
    }

    private function generatePath(string $path)
    {
        $this->timestamp = '?_=' . (new \Datetime)->getTimestamp();
        return $path . $this->suffix . $this->timestamp;
    }

    private function savePemilih(string $json, array $meta)
    {
        $arrayPemilih = json_decode($json, true);
        $index = 0;
        foreach ($arrayPemilih['aaData'] as $calonPemilih) {
            if (!isset($calonPemilih['nik'])) {
                print "Nothing to do.\n";
                break;
            }
            $index++;
            $pemilih = new Pemilih;
            $pemilih->setNama($calonPemilih['nama'])
                ->setNik($calonPemilih['nik'])
                ->setJenisKelamin($calonPemilih['jenisKelamin'])
                ->setAlamat($meta['kelurahan'])
                ->setKelurahan($meta['kelurahan'])
                ->setKecamatan($meta['kecamatan'])
                ->setKota($meta['kota'])
                ->setProvinsi($meta['provinsi'])
            ;
            $this->em->persist($pemilih);
            if (($index % 20) === 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }
        $this->em->flush();
        $this->em->clear();
    }

    private function scrap($path)
    {
        $client = new Client(['base_uri' => $this->baseURI]);
        $response = $client->request('GET', $this->generatePath($path));
        return $response->getBody()->getContents();
    }

    private function startProducer($io)
    {
        $io->writeln('Memulai proses producer untuk mendapatkan target url, ...');
        $path = '';
        $contents = $this->scrap($path);
        $arrayKota = json_decode($contents, true);

        $totalPemilih = 0;
        foreach ($arrayKota['aaData'] as $kota) {
            $totalPemilih += $kota['totalPemilih'];

            // Scrap data by kota
            $pathKota = $path . $kota['namaKabKota'] . '/';
            $arrayKecamatan = json_decode($this->scrap($pathKota), true);
            $io->section('Scrapping ' . $pathKota);

            foreach ($arrayKecamatan['aaData'] as $kecamatan) {
                // Scrap data by kecamatan
                $pathKecamatan = $pathKota . $kecamatan['namaKecamatan'] . '/';
                $arrayKelurahan = json_decode($this->scrap($pathKecamatan), true);
                $io->section('Scrapping ' . $pathKecamatan);

                foreach ($arrayKelurahan['aaData'] as $kelurahan) {
                    // Persist target into database
                    $indexTps = 1;
                    while ($indexTps <= $kelurahan['jmlTps']) {
                        $targetUrl = $pathKecamatan . $kelurahan['namaKelurahan'] . '/' . $indexTps .'/';
                        $targetEntity = new Target;
                        $targetEntity->setUrl($targetUrl)
                            ->setStatus(1)
                        ;
                        $this->em->persist($targetEntity);
                        $indexTps++;
                    }
                    $this->em->flush();
                }
            }
        }
        $io->success('Target url telah didapat.');
    }

    private function startWorker($io, $output)
    {
        $io->section('Memulai worker proses ...');

        $target = $this->targetRepository->findOneBy(['status' => 1]);

        ProgressBar::setFormatDefinition(
            'custom',
            '<info>%elapsed%:%memory%</info> - <fg=white;bg=blue>%message%</>'
        );
        $progressBar = new ProgressBar($output);
        $progressBar->setFormat('custom');
        $progressBar->setMessage('Memulai proses scrapping ...');
        $progressBar->start();

        while ($target) {
            $progressBar->setMessage('Target url: ' . $this->generatePath($target->getUrl()));
            $progressBar->advance();
            $meta = explode('/', $target->getUrl());
            $meta = [
                'provinsi' => 'BANTEN',
                'kota' => $meta[0],
                'kecamatan' => $meta[1],
                'kelurahan' => $meta[2],
            ];

            $target->setStatus(0);
            $this->savePemilih($this->scrap($target->getUrl()), $meta);
            $target = $this->targetRepository->findOneBy(['status' => 1]);
        }
        $this->em->flush();

        $progressBar->finish();
        print 'Selesai' . PHP_EOL;
        return;
    }
}

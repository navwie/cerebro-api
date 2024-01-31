<?php

namespace App\Console\Commands;

use App\Models\Sites;
use Aws\Acm\AcmClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class checkSSL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkSSL:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sites = Sites::whereNotNull(['cert_arn',])
            ->whereNull('is_ssl')->get();

        if (count($sites)) {
            foreach ($sites as $site) {
                try {
                    $acm = new AcmClient([
                        'region' => config('aws.region'),
                        'version' => 'latest'
                    ]);

                    $certArn = $site->cert_arn;

                    $pendingCert = $acm->describeCertificate([
                        'CertificateArn' => $certArn
                    ]);
                    $status = $pendingCert->search('Certificate.Status');
                    if ($status == Sites::CERT_ISSUED) {
                        $site->cert_status = Sites::CERT_ISSUED;
                        $site->save();
                        $site->addAlbCertificateListener();
                    }

                } catch (\Exception $exception) {
                    Log::channel('ssl_management')->error($exception->getMessage());
                    return Command::FAILURE;
                }
            }
        }

        return Command::SUCCESS;
    }
}

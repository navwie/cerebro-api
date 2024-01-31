<?php

namespace app\Console\Commands;

use Aws\AutoScaling\AutoScalingClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;

class protection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'protection:run {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to enable or disable autoscaling protection for EC2 instance.';

    /**
     * Execute the console command.
     *
     * @return int
     */

    const PROTECT = 'enable';

    public function handle()
    {
        $client = new AutoScalingClient([
            'region' => config('aws.region'),
            'version' => 'latest'
        ]);

        $asGroups = $client->describeAutoScalingGroups();
        $asGroupName = '';
        foreach ($asGroups->get('AutoScalingGroups') as $asg) {
            if (str_contains($asg['AutoScalingGroupName'], config('aws.apiAGName'))) {
                $asGroupName = $asg['AutoScalingGroupName'];
            }
        }

        $token = Http::withHeaders([
            'X-aws-ec2-metadata-token-ttl-seconds' => 21600
            ])
            ->put(config('aws.tokenEndpoint'))
            ->body();

        $instanceId = Http::withHeaders([
            'X-aws-ec2-metadata-token' => $token,
            ])
            ->get(config('aws.instanceIdEndpoint'))
            ->body();

        $client->setInstanceProtection([
            'AutoScalingGroupName' => $asGroupName,
            'InstanceIds' => [$instanceId],
            'ProtectedFromScaleIn' => $this->argument('action') == self::PROTECT
        ]);

        return Command::SUCCESS;
    }
}

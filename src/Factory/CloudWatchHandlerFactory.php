<?php

namespace ExpressiveLogger\Factory;

use ExpressiveLogger\Exception\InvalidConfigurationException;
use Interop\Container\ContainerInterface;
use Maxbanton\Cwh\Handler\CloudWatch;
use Monolog\Logger;
use Aws\CloudWatchLogs\CloudWatchLogsClient;


final class CloudWatchHandlerFactory
{

    public function __invoke(ContainerInterface $container) : CloudWatch
    {
        $config = $container->get('config');

        if (!isset($config['expressiveLogger']['handlers']['cloudwatch'])) {
            throw new InvalidConfigurationException('Cloudwatch config is not set');
        }

        $cloudwatchConfig = $config['expressiveLogger']['handlers']['cloudwatch'];

        $cloudwatchClient = $container->get($cloudwatchConfig['client']);

        if (!$cloudwatchClient) {
            throw new InvalidConfigurationException('Cloudwatch client not found in container');
        }

        if (!$cloudwatchClient instanceof CloudWatchLogsClient) {
            throw new InvalidConfigurationException('Cloudwatch client have to be instance of \Aws\CloudWatchLogs\CloudWatchLogsClient');
        }

        $group = $cloudwatchConfig['group'] ?? 'app-log';
        $stream = $cloudwatchConfig['stream'] ?? 'default';
        $retention = $cloudwatchConfig['retention'] ?? 14;
        $batchSize = $cloudwatchConfig['batchSize'] ?? 10000;
        $tags = $cloudwatchConfig['tags'] ?? [];
        $level = $cloudwatchConfig['level'] ?? Logger::DEBUG;
        $bubble = $cloudwatchConfig['bubble'] ?? true;
        $createGroup = $cloudwatchConfig['createGroup'] ?? true;
        

        return new CloudWatch($cloudwatchClient, $group,$stream,
                            $retention,$batchSize,$tags,$level,
                            $bubble,$createGroup);
    }
}

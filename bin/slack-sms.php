<?php

require_once 'config.php';
require_once __DIR__ . '/../vendor/autoload.php';

function mmcli_list_message_ids(): array
{
    exec('mmcli -J -m 0 --messaging-list-sms', $output, $result_code);
    $data = json_decode($output[0], true);
    return $data['modem.messaging.sms'];
}

function mmcli_get_message(string $id): array
{
    exec('mmcli -J -s ' . $id, $output, $result_code);
    $data = json_decode($output[0], true);
    return $data['sms'];
}

function mmcli_delete_message(string $id): void
{
    exec('sudo mmcli -m 0 --messaging-delete-sms=' . $id, $output, $result_code);
}

function receive_sms_message(): ?array
{
    $ids = mmcli_list_message_ids();
    if (count($ids) == 0) {
        return null;
    }
    $id = end($ids);
    $sms_message = mmcli_get_message($id);
    mmcli_delete_message($id);
    return $sms_message;
}

function send_message_to_slack(array $sms_message): void
{
    $webhook = SLACK_WEBHOOK_URL;
    $client = new \Maknz\Slack\Client($webhook, [
        'username' => SLACK_USERNAME,
        'channel' => SLACK_CHANNEL,
        'icon' => SLACK_ICON,
    ]);
    $text = "*発信元番号* " . $sms_message['content']['number'] . " *着信番号* " . SMS_NUMBER . "\n";
    $text .= $sms_message['content']['text'];
    $client->send($text);
}

function main()
{
    $sms_message = receive_sms_message();
    if ($sms_message != null) {
        send_message_to_slack($sms_message);
    }
    sleep(1);
}

main();

?>
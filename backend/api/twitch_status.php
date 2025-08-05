<?php

require_once '../config.php';

header('Content-Type: application/json');

// Get user ID from channel name
$user_url = 'https://api.twitch.tv/helix/users?login=' . TWITCH_CHANNEL_NAME;
$ch_user = curl_init($user_url);
curl_setopt($ch_user, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch_user, CURLOPT_HTTPHEADER, [
    'Client-ID: ' . TWITCH_CLIENT_ID,
    'Authorization: Bearer ' . TWITCH_ACCESS_TOKEN
]);
$user_response = curl_exec($ch_user);
curl_close($ch_user);

$user_data = json_decode($user_response, true);
if (empty($user_data['data'])) {
    echo json_encode(['status' => 'offline', 'error' => 'User not found']);
    exit;
}
$user_id = $user_data['data'][0]['id'];

// Check stream status using user ID
$stream_url = 'https://api.twitch.tv/helix/streams?user_id=' . $user_id;
$ch_stream = curl_init($stream_url);
curl_setopt($ch_stream, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch_stream, CURLOPT_HTTPHEADER, [
    'Client-ID: ' . TWITCH_CLIENT_ID,
    'Authorization: Bearer ' . TWITCH_ACCESS_TOKEN
]);
$stream_response = curl_exec($ch_stream);
curl_close($ch_stream);

$stream_data = json_decode($stream_response, true);

if (!empty($stream_data['data'])) {
    echo json_encode(['status' => 'online']);
} else {
    echo json_encode(['status' => 'offline']);
}

<?php

// Sample Campaign Array
$campaigns = [
    [
        'campaign_name' => 'Campaign A',
        'advertiser' => 'Advertiser A',
        'creative_type' => 'image',
        'image_url' => 'http://example.com/imageA.jpg',
        'landing_page_url' => 'http://example.com/landingA',
        'bid_price' => 1.50,
        'device_compatible' => ['mobile', 'desktop'],
        'geolocation' => 'US'
    ],
    [
        'campaign_name' => 'Campaign B',
        'advertiser' => 'Advertiser B',
        'creative_type' => 'image',
        'image_url' => 'http://example.com/imageB.jpg',
        'landing_page_url' => 'http://example.com/landingB',
        'bid_price' => 1.75,
        'device_compatible' => ['mobile'],
        'geolocation' => 'CA'
    ]
    // Add more campaigns as needed
];

// Sample Bid Request JSON
$bidRequestJson = '{
    "device": "mobile",
    "geolocation": "US",
    "ad_format": "banner",
    "bid_floor": 1.00
}';

// Parse Bid Request
$bidRequest = json_decode($bidRequestJson, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die(json_encode(['error' => 'Invalid JSON']));
}

// Validate Bid Request
$requiredFields = ['device', 'geolocation', 'ad_format', 'bid_floor'];
foreach ($requiredFields as $field) {
    if (!isset($bidRequest[$field])) {
        die(json_encode(['error' => "Missing required field: $field"]));
    }
}

// Select Campaign
$selectedCampaign = null;
foreach ($campaigns as $campaign) {
    if (
        in_array($bidRequest['device'], $campaign['device_compatible']) &&
        $bidRequest['geolocation'] === $campaign['geolocation'] &&
        $bidRequest['bid_floor'] <= $campaign['bid_price']
    ) {
        if ($selectedCampaign === null || $campaign['bid_price'] > $selectedCampaign['bid_price']) {
            $selectedCampaign = $campaign;
        }
    }
}

// Generate Response
if ($selectedCampaign) {
    $response = [
        'campaign_name' => $selectedCampaign['campaign_name'],
        'advertiser' => $selectedCampaign['advertiser'],
        'creative_type' => $selectedCampaign['creative_type'],
        'image_url' => $selectedCampaign['image_url'],
        'landing_page_url' => $selectedCampaign['landing_page_url'],
        'bid_price' => $selectedCampaign['bid_price'],
        'ad_id' => uniqid('ad_', true),
        'creative_id' => uniqid('creative_', true)
    ];
} else {
    $response = ['error' => 'No suitable campaign found'];
}

// Output Response
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
<?php
// API endpoint to improve job description using OpenAI GPT
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$description = isset($data['description']) ? trim($data['description']) : '';
$apiKey = ''; // ضع مفتاحك هنا

if (!$description) {
    echo json_encode(['error' => 'الوصف فارغ']);
    exit;
}

$payload = [
    "model" => "gpt-4o",
    "messages" => [
        [
            "role" => "system",
            "content" => "You analyze the quality of responses produced by LLMs based on the instructions provided in the prompt."
        ],
        [
            "role" => "user",
            "content" => "for the given text:\n\"$description\"\n\nRewrite the text as a short, concise, and professional job description suitable for a job posting (in Arabic). Add only the most important details that help a job seeker understand the requirements. At the end of the description, clearly state in a separate sentence in Arabic which of the following professions are suitable for this job, based on the description:\n\n- نجار: هو الشخص الذي يعمل في الخشب وتركب له\n- حداد: الذي يعمل في الحدادة والحديد\n- موسرجي\n- كهربائي\n- تنظيف\n- مكيانيكي\n- أخري\n\nPlease provide the response in the following JSON format:\n{\n  \"job_description\": \"<>\",\n  \"skills\": [\n    {\n      \"value\": \"حداد\",\n      \"score\": 9\n    }\n  ]\n}\n\nThis is an example — do not use exact values, but keep the format."
        ]
    ],
    "max_tokens" => 200
];

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($httpcode !== 200) {
    echo json_encode(['error' => 'فشل الاتصال بواجهة الذكاء الاصطناعي', 'details' => $response]);
    exit;
}

$result = json_decode($response, true);
if (!isset($result['choices'][0]['message']['content'])) {
    echo json_encode(['error' => 'استجابة غير متوقعة من الذكاء الاصطناعي']);
    exit;
}

// استخراج JSON من النص الناتج
$content = $result['choices'][0]['message']['content'];
$jsonStart = strpos($content, '{');
$jsonEnd = strrpos($content, '}');
if ($jsonStart === false || $jsonEnd === false) {
    echo json_encode(['error' => 'لم يتم العثور على JSON في الرد']);
    exit;
}
$jsonString = substr($content, $jsonStart, $jsonEnd - $jsonStart + 1);

$final = json_decode($jsonString, true);
if (!$final) {
    echo json_encode(['error' => 'فشل في قراءة JSON الناتج', 'raw' => $content]);
    exit;
}
echo json_encode($final);

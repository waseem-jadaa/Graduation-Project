<?php
// API endpoint to review job content for appropriateness using OpenAI GPT
header('Content-Type: application/json');

// Allow cross-origin requests if needed, for development purposes.
// Consider restricting this in production.
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: POST');
// header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$description = isset($data['description']) ? trim($data['description']) : '';
$title = isset($data['title']) ? trim($data['title']) : ''; // Assuming title is also passed for better context

// IMPORTANT: Replace 'YOUR_ACTUAL_OPENAI_API_KEY' with your real OpenAI API key.
// For production environments, consider storing this securely (e.g., environment variables).
$apiKey = '';

if (!$description && !$title) {
    echo json_encode(['error' => 'العنوان والوصف فارغان']);
    exit;
}

// Define inappropriate content criteria clearly for the AI
// Using the provided Arabic prompt for better accuracy
$prompt = "قم بتحليل وصف الوظيفة التالي (العنوان: \"{$title}\"، الوصف: \"{$description}\")، وقرر إذا ما كان مناسبًا للنشر على منصة وظائف عامة.\n\nيُمنع نشر الوظائف التي تحتوي على أي من الأمور التالية:\n- محتوى سياسي (مثل الدعوة لمسيرة أو نشاط سياسي)\n- محتوى عنيف أو إجرامي (مثل القتل، التهريب، التهديد)\n- محتوى غير أخلاقي (مثل عروض جنسية أو ترويج للدخان أو الكحول)\n- محتوى غير قانوني أو ضار بالمجتمع\n\nأجب فقط بصيغة JSON على الشكل التالي:\n{\n  \"suitable\": true/false,\n  \"reason\": \"سبب الرفض إن وجد (بالعربية)\"\n}";

$payload = [
    "model" => "gpt-3.5-turbo", // Using a potentially faster model for review
    "messages" => [
        [
            "role" => "system",
            "content" => "You are a content moderation assistant. Your task is to evaluate job posting content based on provided criteria and indicate suitability."
        ],
        [
            "role" => "user",
            "content" => $prompt
        ]
    ],
    "max_tokens" => 150 // Adjusted tokens, focused on the boolean and short reason
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
$curl_error = curl_error($ch); // Capture cURL errors
curl_close($ch);

if ($curl_error) {
     http_response_code(500); // Internal Server Error
     echo json_encode(['error' => 'cURL error: ' . $curl_error]);
     exit;
}


if ($httpcode !== 200) {
    // Try to decode the error response from OpenAI
    $error_details = json_decode($response, true);
    $error_message = 'فشل الاتصال بواجهة الذكاء الاصطناعي';
    if (isset($error_details['error']['message'])) {
        $error_message .= ': ' . $error_details['error']['message'];
    } else {
         $error_message .= ' (HTTP Code: ' . $httpcode . ')';
    }
    http_response_code($httpcode); // Use the same HTTP code from OpenAI
    echo json_encode(['error' => $error_message, 'details' => $response]);
    exit;
}

$result = json_decode($response, true);

// Check if the expected structure exists
if (!isset($result['choices'][0]['message']['content'])) {
     http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'استجابة غير متوقعة من الذكاء الاصطناعي', 'raw_response' => $result]);
    exit;
}

// Extract JSON string from the content (AI might wrap it in text)
$content = $result['choices'][0]['message']['content'];
$jsonStart = strpos($content, '{');
$jsonEnd = strrpos($content, '}');

if ($jsonStart === false || $jsonEnd === false) {
    // Fallback: if no JSON found, assume unsuitable as a safety measure or flag for manual check
    // Or, ideally, return an error indicating unexpected AI output format
     http_response_code(500); // Indicate issue with AI format
    echo json_encode(['error' => 'لم يتم العثور على JSON بالصيغة المطلوبة في الرد', 'raw_content' => $content]);
    exit;
}

$jsonString = substr($content, $jsonStart, $jsonEnd - $jsonStart + 1);

$ai_evaluation = json_decode($jsonString, true);

if (!$ai_evaluation || !isset($ai_evaluation['suitable'])) {
    // Fallback: if JSON parsing fails or 'suitable' key is missing
     http_response_code(500); // Indicate issue with AI JSON content
    echo json_encode(['error' => 'فشل في قراءة JSON الناتج أو مفتاح suitable مفقود', 'raw_json_string' => $jsonString]);
    exit;
}

// Determine suggested status based on AI evaluation
$suggested_status = $ai_evaluation['suitable'] ? 'published' : 'pending_review';
$reason = $ai_evaluation['reason'] ?? ''; // Get reason if available

echo json_encode([
    'suggested_status' => $suggested_status,
    'reason' => $reason // Include the reason in the response
]);

?>
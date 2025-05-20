<?php
header("Content-Type: application/json; charset=utf-8");

$apiKey = "Eu6ZWlzwOA6jDTrkn4Y1dTCL";
$secretKey = "lSa4Fy22ZGAfL5qn4D1AxCN0S8Ch4qo8";

// 获取 AccessToken
$tokenUrl = "https://aip.baidubce.com/oauth/2.0/token?grant_type=client_credentials&client_id={$apiKey}&client_secret={$secretKey}";
$tokenResponse = file_get_contents($tokenUrl);
if ($tokenResponse === false) {
    echo json_encode(["error" => "无法获取 AccessToken，请检查网络或 API Key 设置"]);
    exit;
}
$tokenData = json_decode($tokenResponse, true);
if (!isset($tokenData['access_token'])) {
    echo json_encode(["error" => "无法获取 AccessToken，返回数据错误"]);
    exit;
}
$accessToken = $tokenData['access_token'];

// 获取前端提交的请求数据
$requestBody = file_get_contents("php://input");
if (!$requestBody) {
    echo json_encode(["error" => "没有接收到请求数据"]);
    exit;
}

// 构造调用百度文心一言 API 的 URL
$apiUrl = "https://aip.baidubce.com/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/completions_pro?access_token={$accessToken}";

// 初始化 cURL 会话
$ch = curl_init();

// 设置 cURL 选项
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json"
));

// 执行 cURL 请求
$response = curl_exec($ch);

// 检查 cURL 错误
if (curl_errno($ch)) {
    echo json_encode(["error" => "cURL 错误: " . curl_error($ch)]);
    curl_close($ch);
    exit;
}

// 关闭 cURL 会话
curl_close($ch);

// 返回 API 响应
echo $response;
?>

<?php
namespace App\Controllers;
use App\Helpers\BranchNameHelper;
use App\Models\AuthModelfp;
use App\Models\User;
use App\Models\Branch;

// 1. BaseControllerን እንዲወርስ (Extends) እናደርጋለን
class AuthControllerfp extends BaseController {
    
public function forgotPassword() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       if (isset($_POST['send_code'])) {
        $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
        // እዚህ ጋር ስልክ ቁጥሩን በDB መፈለግ እና ኮድ የመላክ ስራዎን ይስሩ
    
        $model = new AuthModelfp($this->db);
        $result = $model->createResetToken($phone);

        if ($result) {
            $message = "ውድ " . $result['username'] . "፣ የይለፍ ቃል መለወጫ ኮድዎ: " . $result['token'];
            $this->sendSMS($phone, $message);
            $data['message'] = "የተጠቃሚ ስምዎ እና ኮዱ ወደ ስልክዎ ተልከዋል።";
        } else {
            $data['error'] = "ስልክ ቁጥሩ አልተገኘም ወይም ተጠቃሚው ታግዷል።";
        }
       }
    }
    $this->renderwithoutlogin('forgot-password', $data ?? []);
}
// ያንተን የ cURL SMS ተግባር እዚህ አስቀምጠው
/**
 * የ SMS መላኪያ ተግባር
 * ይህ ተግባር በAuthController ውስጥ ይኖራል
 */
private function sendSMS($phone, $message) {
    // ስልክ ቁጥርን ወደ 251 ፎርማት መቀየር
    $formatted_phone = "251" . ltrim(trim($phone), '0');

    // cURL setup
    $ch = curl_init('https://smsethiopia.et/api/sms/send');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'KEY: 6G5AYFVB751EID5XXA1O0T7I8EVXR00Z:406', 
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    // መረጃውን በ JSON መልክ ማዘጋጀት
    $post_data = json_encode([
        'msisdn' => $formatted_phone,
        'text' => $message
    ]);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    
    // መላክ
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    // ውጤቱን መፈተሽ
    if ($error) {
        error_log("SMS Error to $phone: " . $error);
        return false;
    }
    return $response;
}
}
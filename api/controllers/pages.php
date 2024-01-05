<?php
/**
* @OA\Info(title="fiaxit API Live Test", version="1.0")
*   @OA\SecurityScheme(
*       type="http",
*       description=" Use /auth to get the JWT token",
*       name="Authorization",
*       in="header",
*       scheme="bearer",
*       bearerFormat="JWT",
*       securityScheme="bearerAuth",
*   )
*/

require $_SERVER['DOCUMENT_ROOT'].'/app-with-api-main/api/vendor/autoload.php';

use \Firebase\JWT\JWT;

class Pages {
    private $conn;

    private $key = 'privatekey';

    private $db_table = 'pages';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
    * @OA\Get(path="/app-with-api-main/api/v1/pages/auth_token", tags={"Auth"},
    * @OA\Response (response="200", description="Success"),
    * @OA\Response (response="404", description="Not Found"),
    * )
    */

    public function auth_token(){
        $iat = time();
        $exp = $iat + 60 * 60;
        $payload = array(
            'iss' => 'http://liveapi.local/api', //issuer
            'aud' => 'http://livetest.local/', //audience
            'iat' => $iat, //time JWT was issued
            'exp' => $exp //time JWT expires
        );
        $jwt = JWT::encode($payload, $this->key, 'HS512');
        return array(
            'jwt_token'=>$jwt,
            'expires'=>$exp
        );
    }

    /**
    * @OA\Post(path="/app-with-api-main/api/v1/pages/signin", tags={"Auth"},
    *   @OA\RequestBody(
    *       @OA\MediaType(
    *           mediaType="json",
    *           @OA\Schema(required={"email", "password"},   
    *               @OA\Property(property="email", type="string", example="email or phone_no"),
    *               @OA\Property(property="password", type="string")
    *           )
    *       )
    *   ),
    *   @OA\Response (response="200", description="Success"),
    *   @OA\Response (response="404", description="Not Found"),
    *   security={ {"bearerAuth":{}}}
    * )
    */
    public function signin() {
        try {
            $query = "SELECT user_id, email, phone_no, password FROM users WHERE (email = :email OR phone_no = :email)";
            
            $stmt = $this->conn->prepare($query);
        
            $stmt->bindParam(":email", $this->email);
        
            $stmt->execute();
        
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
                $providedPasswordHash = hash('sha512', $this->password);
    
                if ($user['password'] === $providedPasswordHash) {
                    unset($user['password']); // Remove password from user data
                    $user["token"] = $this->auth_token();
                    return [
                        "status" => "2",
                        "user" => $user 
                    ];
                } else {
                    // Incorrect password
                    return [
                        "status" => "4",
                        "message" => "Incorrect password"
                    ];
                }
            } else {
                // User with provided email/phone does not exist
                return [
                    "status" => "3",
                    "message" => "User does not exist"
                ];
            }
        } catch (PDOException $e) {
            // Catch database errors
            return [
                "status" => "error",
                "message" => "Database error occurred: " . $e->getMessage()
            ];
        }
    }
    
    


    /**
    * @OA\Post(path="/app-with-api-main/api/v1/pages/signup", tags={"Auth"},
    *   @OA\RequestBody(
    *       @OA\MediaType(
    *           mediaType="json",
    *           @OA\Schema(required={"user_role","phone_no", "email", "password"},
    *               @OA\Property(property="user_role", type="string", example="1 for users, 2 for admin"),
    *               @OA\Property(property="phone_no", type="string"),    
    *               @OA\Property(property="email", type="string"),
    *               @OA\Property(property="password", type="string")
    *           )
    *       )
    *   ),
    *   @OA\Response (response="200", description="Success"),
    *   @OA\Response (response="404", description="Not Found"),
    *   security={ {"bearerAuth":{}}}
    * )
    */
    public function signup() {
        try {
            $query = "SELECT * FROM users WHERE email = :email OR phone_no = :phone_no";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":phone_no", $this->phone_no);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return [
                    "status" => "1",
                    "message" => "User already exists"
                ];
            } else {
                $hashedPassword = hash('sha512', $this->password);

    
                $insertQuery = "INSERT INTO users (email, phone_no, password, user_role) VALUES (:email, :phone_no, :password, :user_role)";
                $insertStmt = $this->conn->prepare($insertQuery);
                $insertStmt->bindParam(":email", $this->email);
                $insertStmt->bindParam(":phone_no", $this->phone_no);
                $insertStmt->bindParam(":password", $hashedPassword);
                $insertStmt->bindParam(":user_role", $this->user_role);
    
                if ($insertStmt->execute()) {
                    return [
                        "status" => "2",
                        "message" => "Signup successful"
                    ];
                } else {
                    return [
                        "status" => "error",
                        "message" => "Signup failed. Please try again."
                    ];
                }
            }
        } catch (\PDOException $e) {
            return [
                "status" => "error",
                "message" => "Database error occurred",
                "errorInfo" => $e->errorInfo
            ];
        } catch (\Exception $e) {
            return [
                "status" => "error",
                "message" => "Error occurred. Please try again.",
                "errorInfo" => $e->getMessage()
            ];
        }
    }
    
    

}
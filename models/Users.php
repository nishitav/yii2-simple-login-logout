<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $user_id
 * @property string $email
 * @property string $password
 * @property string|null $password_reset_token
 * @property string|null $last_login_at
 * @property string $registration_ip
 * @property int $is_active
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property Profile $profile
 */
class Users extends ActiveRecord implements IdentityInterface {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['email', 'password'], 'required'],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 100],
            [['password'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'User ID',
            'email' => 'Email',
            'password' => 'Password',
            'password_reset_token' => 'Password Reset Token',
            'is_active' => 'Status',
            'last_login_at' => 'Last Login At',
            'registration_ip' => 'Registration Ip',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfiles() {
        return $this->hasMany(Profile::className(), ['user_id' => 'user_id']);
    } 

    /**
     * Gets query for [[Profile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
    }

    /** INCLUDE USER LOGIN VALIDATION FUNCTIONS* */

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    /* modified */
    public static function findIdentityByAccessToken($token, $type = null) {
        if (Yii::$app->params['apiToken'] === $token) {
            return true;
        }

        return false;
    }

    /* removed
      public static function findIdentityByAccessToken($token)
      {
      throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
      }
     */

    /**
     * Finds user by email
     *
     * @param  string      $email
     * @return static|null
     */
    public static function findByEmail($email) {
        return static::findOne(['email' => $email]);
    }

    /**
     * Finds user by password reset token
     *
     * @param  string      $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token) {
        $expire = \Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        //return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        //return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password) {
        return password_verify($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password_hash = Security::generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->auth_key = Security::generateRandomKey();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Security::generateRandomKey() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }

    /**
     * Before save
     *
     * @param bool $insert
     *
     * @return bool
     */
    public function beforeSave($insert) {
        $currentTime = date('Y-m-d H:i:s');
        if ($this->isNewRecord) {

            $this->created_at = $currentTime;
            if (!Yii::$app->user->isGuest) {
                $this->created_by = Yii::$app->user->identity->id;
            }
        }

        $this->updated_at = $currentTime;
        if (!Yii::$app->user->isGuest) {
            $this->updated_by = Yii::$app->user->identity->id;
        }

        return parent::beforeSave($insert);
    }
    
    public function updateUserPassword($param) {
        $user = Users::findOne(['user_id' => Yii::$app->user->identity->id]);
        if (isset($user)) {
            $user->password = password_hash($param["RegisterForm"]["password"],  PASSWORD_BCRYPT, [
                'cost' => 12,
            ]);
            $user->save();
        }
    }

    public function creareUser($param) {
        // check user already exists then skip
        $user = Users::findOne(['email' => trim($param["RegisterForm"]['email'])]);
        $sendMail = false;

        if (!isset($user)) {
            $user = new \app\models\Users();
            $user->email = trim($param["RegisterForm"]['email']);
            $user->registration_ip = Yii::$app->getRequest()->getUserIP();
            $roleId = filter_var(Yii::$app->request->get('type'), FILTER_SANITIZE_NUMBER_INT);
            $user->user_role = $roleId;
            $sendMail = true;
        }

        if (isset($param["RegisterForm"]["password"])) {

            if ($param["RegisterForm"]["password"] !== "") { // check for profile request where not necessary to update password.
                Yii::$app->session->set('user-password', $param["RegisterForm"]["password"]);
                $user->password = password_hash($param["RegisterForm"]["password"],  PASSWORD_BCRYPT, [
                    'cost' => 12,
                ]);
            }
        } else {
            Yii::$app->session->set('user-password', \app\helpers\CommonHelper::randomPassword());
            $user->password = password_hash(Yii::$app->session->get('user-password'),  PASSWORD_BCRYPT, [
                'cost' => 12,
            ]);
        }

        $user->save();

        // check Profile already exists then skip
        $profile = Profile::findOne(['user_id' => $user->user_id]);
        if (!isset($profile)) {
            $profile = new Profile();
            $profile->user_id = $user->user_id;
            $profile->referral_code = uniqid();
            $profile->parent_referral_code = !empty($param["RegisterForm"]["parent_referral_code"])?$param["RegisterForm"]["parent_referral_code"]:null;
        }
        
        $profile->first_name = $param["RegisterForm"]['first_name'];
        $profile->last_name = $param["RegisterForm"]['last_name'];
        $profile->company_name = !empty( $param["RegisterForm"]['company_name'] !== '') ? $param["RegisterForm"]['company_name'] : NULL;
        $profile->address_1 = !empty( $param["RegisterForm"]['address_1'] !== '') ? $param["RegisterForm"]['address_1'] : NULL;
        $profile->address_2 = !empty( $param["RegisterForm"]['address_2'] !== '') ? $param["RegisterForm"]['address_2'] : NULL;
        $profile->city = !empty( $param["RegisterForm"]['city'] !== '') ? $param["RegisterForm"]['city'] : NULL;
        $profile->province = !empty( $param["RegisterForm"]['province'] !== '') ? $param["RegisterForm"]['province'] : NULL;
        $profile->zip_code = !empty( $param["RegisterForm"]['zip_code'] !== '') ? $param["RegisterForm"]['zip_code'] : NULL;
        $profile->phone_number = $param["RegisterForm"]['phone_number'];
        $profile->country_id = $param["RegisterForm"]['country_id'];

        $profile->save(false);

        // send email to user for registration
        if ($sendMail) {
            $helper = new \app\helpers\EmailHelper();
            $helper->send('New User Registration', $user->email);
        }

        return $user->id;
    }

}

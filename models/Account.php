<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;


/**
 * This is the model class for table "user_account".
 *
 * @author Janki Chaudhari <jankichaudhari@gmail.com>
 *
 * @property integer $id
 * @property int $number
 * @property string $user_name
 * @property double $balance
 * @property double $overdraft
 * @property boolean $active
 * @property string $created
 * @property string $updated
 */
class Account extends ActiveRecord
{
    const ACCOUNT_NUMBER_SERIES = 100000;
    const ACCOUNT_OPEN_DEPOSIT = 0.00;
    const ACCOUNT_OPEN = 1;
    const ACCOUNT_CLOSE = 0;
    const OVERDRAFT_AMOUNT = 1000.00;

    /**
     * Inherited method to set table name
     *
     * @return string
     */
    public static function tableName()
    {
        parent::tableName();
        return 'account';
    }

    /**
     * Inherited method to set rules for validation of the database fields
     *
     * @return array
     */
    public function rules()
    {
        parent::rules();
        return [
            [['number', 'user_name', 'overdraft'], 'required'],
            [['number', 'balance', 'overdraft'], 'number'],
            [['active'], 'boolean'],
            [['created', 'updated'], 'safe'],
            [['user_name'], 'string', 'max' => 300],
            [['number'], 'unique'],
        ];
    }

    /**
     * Inherited method to set attribute labels
     *
     * @return array
     */
    public function attributeLabels()
    {
        parent::attributeLabels();
        return [
            'id' => 'ID',
            'number' => 'Account Number',
            'user_name' => 'User Name',
            'balance' => 'Balance',
            'overdraft' => 'Overdraft',
            'active' => 'Active',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }

    /**
     * This function generate 6 digit account .
     *
     * @return int
     */
    private function _generateAccountNumber()
    {
        /** @var Account $latestAccount */
        $latestAccount = Account::find()->orderBy('number DESC')->one();
        $latestNumber = ($latestAccount && $latestAccount->number) ?
            $latestAccount->number :
            self::ACCOUNT_NUMBER_SERIES;
        $latestNumber++;

        return $latestNumber;
    }

    /**
     * Inherited method called before validation of the fields.
     * Default values added to the record when record created by minimum requirements
     *
     * @return bool
     */
    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            $this->number = $this->_generateAccountNumber();
            $this->balance = self::ACCOUNT_OPEN_DEPOSIT;
            $this->overdraft = 0.00;
            $this->active = self::ACCOUNT_OPEN;
        }

        return parent::beforeValidate();
    }

    /**
     * Returns total balance with overdraft limit
     *
     * @return float
     */
    public function totalBalance()
    {
        return $this->balance + $this->overdraft;
    }

    /**
     * Deposit amount into  balance
     *
     * @param float $amount the fund going to be deposit
     *
     * @return bool
     */
    public function depositFund(float $amount)
    {
        if (!$this->active) {
            return false;
        }

        if ($amount < 0) {
            return false;
        }

        $this->balance = $this->balance + $amount;
        return $this->save();
    }

    /**
     * Apply overdraft if user has balance in the account.
     *
     * @return bool
     */
    public function applyOverdraft()
    {
        if (!$this->active) {
            return false;
        }

        if ($this->overdraft) {
            return true;
        }

        if ($this->balance) {
            $this->overdraft = self::OVERDRAFT_AMOUNT;
            return $this->save();
        }

        return false;
    }

    /**
     * Update balance and/or overdraft as per withdrawn amount.
     *
     * @param float $amount the fund going to be withdrawn
     *
     * @return bool
     */
    public function withdrawFund(float $amount)
    {
        if (!$this->active) {
            return false;
        }

        if ($amount < 0) {
            return false;
        }

        if (!$this->balance && !$this->overdraft) {
            return false;
        }

        $total = ($this->totalBalance()) - $amount;

        if ($total >= 0) {
            $diff = $this->balance - $amount;
            if ($diff >= 0) {
                $this->balance = $diff;
            } else {
                $this->balance = 0;
                $this->overdraft = $this->overdraft + $diff;
            }
            return $this->save();
        } else {
            return false;
        }

    }

    /**
     * This method will inactive account.
     *
     * @return bool
     */
    public function closeAccount()
    {
        if (!$this->active) {
            return false;
        }

        $this->active = self::ACCOUNT_CLOSE;
        return $this->save();
    }

    /**
     * Retrieve account object by it's account number
     *
     * @param int $accountNumber
     *
     * @return Account|bool
     */
    public static function getAccountByNumber(int $accountNumber)
    {
        /** @var Account $account */
        $account = Account::find()->where(['number' => $accountNumber])->one();

        if (!$account) {
            return false;
        }

        return $account;
    }
}

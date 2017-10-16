<?php
namespace models;

use \Codeception\Test\Unit;
use app\models\Account;


/**
 * Unit tests for Account model
 *
 * @author Janki Chaudhari <jankichaudhari@gmail.com>
 *
 */
class AccountTest extends Unit
{
    /**
     * Dataprovider for testDepositFund
     *
     * @return array
     */
    public function depositsData()
    {
        return [
            [100001, 1000, 1],
            [100001, 0, 1],
            [100001, 000, 1],
            [100001, -1000, 0],
            [100001, '1000', 1],
            [100001, (float)null, 1],
            [100003, 1000, 0],
            [100005, 1000, 0],
            [100005, 0, 0],
            [9999, 1000, 0],
        ];
    }

    /**
     * Dataprovider for testApplyOverdraft
     *
     * @return array
     */
    public function overdraftsData()
    {
        return [
            [100001, 1],
            [100002, 0],
            [100003, 0],
            [100004, 1],
            [100005, 0],
            [100006, 1],
            [9999, 0],
        ];
    }

    /**
     * Dataprovider for testWithDrawFund
     *
     * @return array
     */
    public function withdrawData()
    {
        return [
            [100001, 1000, 1],
            [100001, 10952, 1],
            [100001, 13000, 0],
            [100001, -1000, 0],
            [100001, '1000', 1],
            [100001, (float)null, 1],
            [100002, 1000, 0],
            [100003, 1000, 0],
            [100004, 200, 1],
            [100004, 250, 0],
            [100005, 1000, 0],
            [100006, 6000, 0],
            [9999, 600, 0],
        ];
    }

    /**
     * Dataprovider for testCloseAccount
     *
     * @return array
     */
    public function closeAccountData()
    {
        return [
            [100001, 1],
            [100002, 1],
            [100003, 0],
            [100004, 1],
            [100005, 0],
            [100006, 1],
            [9999, 0],
        ];
    }

    /**
     * Mock users to insert bank account record in database
     *
     * @return array
     */
    public function mockInsertUsers()
    {
        return [
            ['test record 0', 1],
            [
                'testrecord1testrecord1testrecord1testrecord1testrecord1testrecord1testrecord1testrecord1
                testrecord1testrecord1testrecord1testrecord1testrecord1testrecord1testrecord1testrecord1testrecord1
                testrecord1testrecord1testrecord1testrecord1testrecord1testrecord1testrecord1testrecord1testrecord1
                testrecord1testrecord1testrecord1testrecord1tooooooooolong',
                0
            ],
            ['test_record_3', 1],
            ['12345678900876', 1],
            [12345678900876, 0],
            [null, 0],
            ['null', 1],
            ['', 0],
        ];
    }


    /**
     * Test suite for createAccount() method
     *
     * @param string $username `user_name` field
     * @param bool   $expected Expected result
     *
     * @dataProvider mockInsertUsers
     *
     * @return \Codeception\Verify
     */
    public function testCreateAccount($username, $expected)
    {
        $account = new Account();
        $account->user_name = $username;

        return expect($account->save())->equals($expected);

    }

    /**
     * Test method of Account model and revert values of given fields
     *
     * @param string $methodName   public Method name of Account to be tested
     * @param array  $methodParams parameters of the methodName
     * @param bool   $expected     expected result from test
     * @param int    $accNumber    Account Number
     * @param array  $fields       Fields of the Account model needs to be reverted
     *
     * @return bool|void
     */
    private function _testMethod(
        string $methodName,
        array $methodParams,
        bool $expected,
        int $accNumber,
        array $fields
    ) {
        $account = Account::getAccountByNumber($accNumber);
        $result = true;

        if ($account) {
            foreach ($fields as $field) {
                $pre = 'pre' . $field;
                $$pre = $account->{$field};
            }

            $method = $account->{$methodName}(implode(",", $methodParams));
            $result = expect($method)->equals($expected);

            //keep record values original. So can do correct tests next time
            foreach ($fields as $field) {
                $pre = 'pre' . $field;
                $account->{$field} = $$pre;
            }
            $account->save();
        }

        return $result;
    }

    /**
     * Test suite for closeAccount()
     *
     * @param int  $accNumber `number` field
     * @param bool $expected  Expected result
     *
     * @dataProvider closeAccountData
     *
     * @return \Codeception\Verify
     */
    public function testCloseAccount($accNumber, $expected)
    {
        return $this->_testMethod(
            'closeAccount',
            [],
            $expected,
            $accNumber,
            ['active']
        );
    }

    /**
     * Test suite for depositFund()
     *
     * @param int   $accNumber `number` field
     * @param float $amount    amount to be added to the balance
     * @param bool  $expected  Expected result
     *
     * @dataProvider depositsData
     *
     * @return \Codeception\Verify
     */
    public function testDepositFund($accNumber, $amount, $expected)
    {
        return $this->_testMethod(
            'depositFund',
            [$amount],
            $expected,
            $accNumber,
            ['balance']
        );
    }

    /**
     * Test suite for applyOverdraft()
     *
     * @param int  $accNumber `number` field
     * @param bool $expected  Expected result
     *
     * @dataProvider overdraftsData
     *
     * @return \Codeception\Verify
     */
    public function testApplyOverdraft($accNumber, $expected)
    {
        return $this->_testMethod(
            'applyOverdraft',
            [],
            $expected,
            $accNumber,
            ['overdraft']
        );
    }

    /**
     * Test suite for withdrawFund()
     *
     * @param int   $accNumber `number` field
     * @param float $amount    amount to be added to the balance
     * @param bool  $expected  Expected result
     *
     * @dataProvider withdrawData
     *
     * @return \Codeception\Verify
     */
    public function testWithdrawFund($accNumber, $amount, $expected)
    {
        return $this->_testMethod(
            'withdrawFund',
            [$amount],
            $expected,
            $accNumber,
            ['balance', 'overdraft']
        );
    }
}

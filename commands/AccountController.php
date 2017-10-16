<?php
namespace app\commands;

use app\models\Account;
use yii\console\Controller;

/**
 * AccountController handles all commands to access bank account services
 *
 * @author Janki Chaudhari <jankichaudhari@gmail.com>
 *
 */
class AccountController extends Controller
{
    /**
     * This command will open new bank account for the user by taking username.
     *
     * @param string $name the name of the user going to be created.
     * If the name has space in between then pass them surrounded by quotes
     * example 'John Smith'
     *
     * @return string
     */
    public function actionCreate(string $name)
    {
        $account = new Account();
        $account->user_name = $name;
        $r = $account->save();

        if ($r) {
            $message = " User account for $name created successfully...\n";
            $message .= " Your account number is : {$account->number} \n ";
            $message .= " Please save it somewhere,";
            $message .= " so you can use it to access other services. ";
            return $this->_exitMessage(Controller::EXIT_CODE_NORMAL, $message);
        } else {
            $message = "Error! User account for $name not created!";
            return $this->_exitMessage(Controller::EXIT_CODE_ERROR, $message);
        }
    }

    /**
     * This command will display balance of the user's bank account
     *
     * @param int $number the account number which is 6 digit integer here.
     *
     * @return string
     */
    public function actionBalance($number)
    {
        $account = $this->_currentAccount($number);

        $totalBalance = $account->totalBalance();
        $message = "{$account->user_name} has \n  balance : {$account->balance}\n";
        $message .= "  Overdraft limit : {$account->overdraft}\n\r";
        $message .= "  Total Balance : {$totalBalance}\n\r";

        return $this->_exitMessage(Controller::EXIT_CODE_NORMAL, $message);
    }

    /**
     * This command will deposit user's fund in respective bank account.
     *
     * @param int    $number the account number which is 6 digit integer here.
     * @param double $amount the fund amount going to deposit.
     *
     * @return string
     */
    public function actionDeposit($number, $amount)
    {
        $account = $this->_currentAccount($number);

        if (!$account->depositFund($amount)) {
            return $this->_exitMessage(
                Controller::EXIT_CODE_ERROR,
                "Error! Can't deposit fund."
            );
        }

        $this->actionBalance($number);
        return $this->_exitMessage(
            Controller::EXIT_CODE_NORMAL,
            "Your fund {$amount} added to your balance successfully..."
        );
    }

    /**
     * This command allows to apply overdraft to the user's bank account.
     * The overdraft limit is already set.
     *
     * @param int $number the account number which is 6 digit integer here.
     *
     * @return string
     */
    public function actionApplyOverdraft($number)
    {
        $account = $this->_currentAccount($number);
        $result = "";

        if ($account->overdraft) {
            $message = "Your overdraft is already applied. ";
            $result = $this->_exitMessage(Controller::EXIT_CODE_NORMAL, $message);
        } else {
            if (!$account->balance) {
                $message = "You do not have enough balance to apply overdraft.";
                $message .= "\n Please deposit first.";
                $result = $this->_exitMessage(
                    Controller::EXIT_CODE_NORMAL,
                    $message
                );
            } else {
                if ($account->applyOverdraft()) {
                    $message = "Your overdraft applied successfully...";
                    $result = $this->_exitMessage(
                        Controller::EXIT_CODE_NORMAL,
                        $message
                    );
                }
            }
        }

        $this->actionBalance($number);
        return $result;
    }

    /**
     * This command allows user to withdraw fund from respective bank account.
     *
     * @param int    $number the account number which is 6 digit integer here.
     * @param double $amount the fund amount going to deposit.
     *
     * @return string
     */
    public function actionWithdraw($number, $amount)
    {
        $account = $this->_currentAccount($number);

        if (!$account->withdrawFund($amount)) {
            $message = "Can't withdraw money!";
            $message .= " \n Please check your account balance and overdraft limit.";
            return $this->_exitMessage(Controller::EXIT_CODE_ERROR, $message);
        }

        $message = "You withdrawn {$amount}";
        $result = $this->_exitMessage(Controller::EXIT_CODE_NORMAL, $message);
        $this->actionBalance($number);

        return $result;
    }

    /**
     * This command will close the user account.
     *
     * @param int $number the account number which is 6 digit integer here.
     *
     * @return string
     */
    public function actionClose($number)
    {
        $account = $this->_currentAccount($number);

        if (!$account->closeAccount()) {
            return $this->_exitMessage(
                Controller::EXIT_CODE_ERROR,
                "Error! Could not close the account"
            );
        }

        $message = "{$account->user_name}, Your account has been closed.";
        return $this->_exitMessage(Controller::EXIT_CODE_NORMAL, $message);
    }

    /**
     * Get current active account
     *
     * @param int $number the account number which is 6 digit integer here.
     *
     * @return Account|bool
     */
    private function _currentAccount($number)
    {
        $account = Account::getAccountByNumber($number);

        if (!$account) {
            $this->_exitMessage(
                Controller::EXIT_CODE_ERROR,
                "Error! Can't find this account!"
            );
        }

        if (!$account->active) {
            $this->_exitMessage(
                Controller::EXIT_CODE_ERROR,
                "This account is closed! Not active anymore."
            );
        }

        return $account;

    }

    /**
     * This method used to display message and/or exit on terminal
     *
     * @param int    $code    Exit code
     * @param string $message
     *
     * @return string|exit()
     */
    private function _exitMessage($code = Controller::EXIT_CODE_NORMAL, $message = "")
    {
        if (!$message) {
            $message = ($code == Controller::EXIT_CODE_NORMAL) ?
                "Successfully Done!" :
                "Error! Can't Proceed!";
        }
        $message .= "\n\r";

        echo $message;
        if ($code == Controller::EXIT_CODE_ERROR) {
            exit();
        }
    }
}
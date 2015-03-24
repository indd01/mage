<?php
/*
 * Tools for magento
 * for everyday usage
 * :)
 */
require_once 'app/Mage.php';
$oMyTools = tools::instance();


class tools{

    /*
     * Instance
     */
    static $oInstance;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->getRequest();

    }// function __construct

    /**
     * Instance
     */


    public static function instance()
    {
        if (empty(self::$oInstance)) {
            self::$oInstance = new self();
        }
        return self::$oInstance;
    } // function instance

    /*
     * Get GET request
     */
    public function getRequest()
    {
        $sRequest = explode('/', Mage::app()->getRequest()->getPathInfo());
        $sAction = $sRequest[1];
        $sParam = isset($sRequest[2]) ? $sRequest[2] : null;


        if(!empty($sAction) && method_exists($this, $sAction)){
            return !is_null($sParam) ? $this->$sAction($sParam) : $this->$sAction();
        } else {
            echo 'Welcome to tools, available methods: ';
            echo '<pre>';
            var_export(get_class_methods($this));
            echo '</pre>';
        }

    }

    /*
     * change admin user password
     */
    public function changeAdminPass($nameAndPass)
    {
        $post       = Mage::app()->getRequest()->getPost();
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

        if(!empty($post)){
            $username = $post['user'];
            $password = $post['newpass'];
            $back     = $_SERVER['HTTP_REFERRER'];
            $sql      = "select username from admin_user where username = '$username'";

            if($connection->fetchAll($sql)){
                $sql = "update admin_user set password = CONCAT(MD5('qX$password'), ':qX') where username = '$username'";
                $connection->raw_query($sql);
                echo "Password has been changed!<br /><a href='$back'>back</a>";
            } else {
                echo 'please inupt correct username!';
            }
        } else {
            $sql = "select * from admin_user";
            $row = $connection->fetchAll($sql);
            echo "<table>";
            foreach($row as $k => $v){
                $user = $v['username'];
                $pass = $v['password'];
                echo "<tr><td>$user</td><td>$pass</td></tr>";
            }
            echo "</table>";
            $action = $_SERVER['PHP_SELF'];
            echo "<form action=$action method='post'>"
                    . "user: <input type='text' name='user'> "
                    . "newpss:<input type='text' name='newpass'> "
                    . "<input type='submit' value='change pass'></form>";
        }

    }

    /*
     * mklog
     */
    public function mklog($mData, $sVar){
        $dir = $_SERVER['DOCUMENT_ROOT'].'/my_logs/';
        if(!is_dir($dir)){
            mkdir($dir);
        }
        $file = date('d-m-Y');
        $content = is_array($mData) ? var_export($mData, true) : $mData;
        file_put_contents($dir.$file, date('h-i-s') . ': '. strtoupper($sVar) . ': ' . $content . "\n", FILE_APPEND);
    }

    /*
     * Show all pay modules
     */
    public function payModules()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
	$methods = array(array('value'=>'', 'label'=>Mage::helper('adminhtml')->__('--Please Select--')));
	foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = array(
                'label'   => $paymentTitle,
                'value' => $paymentCode,
            );
        }
        echo '<pre>';
        print_r($methods);
        echo '</pre>';

    }

    /*
     * show all modules
     * or single one
     */
    public function getModules($moduleName = null)
    {
        Mage::app('admin');
        $module = !is_null($moduleName)? 'modules/'.$moduleName : 'modules';
        echo '<pre>';
        $res = Mage::getConfig()->getNode($module);
        if ($res){
            print_r($res->asArray());
        } else {
            echo $moduleName . ': not found!';
        }

        echo '</pre>';
    }

    /*
     * Show full config as XML
     */
    public function getConfig()
    {
        Mage::app('admin');
        header('Content-type: text-xml');
        echo Mage::getConfig()->getNode()->asXML();

    }

    public function getAdminConfig()
    {
        header('Content-Type: text-xml');
        echo Mage::getConfig()->loadModulesConfiguration('system.xml')->getNode()->asXML();
    }
}

?>

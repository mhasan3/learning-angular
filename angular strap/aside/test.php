<!-- try to do with this example -->   
 <html ng-app>
        <head>
            <title>AngularJs Post Example </title>
            <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/angularjs/1.0.7/angular.min.js"></script>
            <style>
                #dv1{
                    border:1px solid #DBDCE9; margin-left:auto;
                    margin-right:auto;width:220px;
                    border-radius:7px;padding: 25px;
                }

                .info{
                    border: 1px solid;margin: 10px 0px;
                    padding:10px;color: #00529B;
                    background-color: #BDE5F8;list-style: none;
                }
                .err{
                    border: 1px solid;  margin: 10px 0px;
                    padding:10px;  color: #D8000C;
                    background-color: #FFBABA;   list-style: none;
                }
            </style>
        </head>
        <body>
            <div id='dv1'>
                <form ng-controller="FrmController">
                    <ul>
                        <li class="err" ng-repeat="error in errors"> {{ error}} </li>
                    </ul>
                    <ul>
                        <li class="info" ng-repeat="msg in msgs"> {{ msg}} </li>
                    </ul>
                    <h2>Sigup Form</h2>
                    <div>
                        <label>Name</label> 
                        <input type="text" ng-model="username" placeholder="User Name" style='margin-left: 22px;'> 
                    </div>
                    <div>
                        <label>Email</label>
                        <input type="text" ng-model="useremail" placeholder="Email" style='margin-left: 22px;'> 
                    </div>
                    <div>
                        <label>Password</label>
                        <input type="password" ng-model="userpassword" placeholder="Password">
                    </div>
                    <button ng-click='SignUp();' style='margin-left: 63px;margin-top:10px'>SignUp</button>
                </form>
            </div>

            <script type="text/javascript">
                function FrmController($scope, $http) {
                    $scope.errors = [];
                    $scope.msgs = [];

                    $scope.SignUp = function() {

                        $scope.errors.splice(0, $scope.errors.length); // remove all error messages
                        $scope.msgs.splice(0, $scope.msgs.length);

                        $http.post('post_es.php', {'uname': $scope.username, 'pswd': $scope.userpassword, 'email': $scope.useremail}
                        ).success(function(data, status, headers, config) {
                            if (data.msg != '')
                            {
                                $scope.msgs.push(data.msg);
                            }
                            else
                            {
                                $scope.errors.push(data.error);
                            }
                        }).error(function(data, status) { // called asynchronously if an error occurs
    // or server returns response with an error status.
                            $scope.errors.push(status);
                        });
                    }
                }
            </script>
        </body>
     </html>
<?php

$data = json_decode(file_get_contents("php://input"));
$usrname = mysql_real_escape_string($data->uname);
$upswd = mysql_real_escape_string($data->pswd);
$uemail = mysql_real_escape_string($data->email);

$con = mysql_connect('localhost', 'root', '1');
mysql_select_db('myDB', $con);

$qry_em = 'select count(*) as cnt from users where email ="' . $uemail . '"';
$qry_res = mysql_query($qry_em);
$res = mysql_fetch_assoc($qry_res);

if ($res['cnt'] == 0) {
    $qry = 'INSERT INTO MyGuests (name,pass,email) values ("' . $usrname . '","' . $upswd . '","' . $uemail . '")';
    $qry_res = mysql_query($qry);
    if ($qry_res) {
        $arr = array('msg' => "User Created Successfully!!!", 'error' => '');
        $jsn = json_encode($arr);
        print_r($jsn);
    } else {
        $arr = array('msg' => "", 'error' => 'Error In inserting record!');
        $jsn = json_encode($arr);
        print_r($jsn);
    }
} else {
    $arr = array('msg' => "", 'error' => 'User Already exists with same email');
    $jsn = json_encode($arr);
    print_r($jsn);
}
?>
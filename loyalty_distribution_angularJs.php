<?php

use CnG\Platform\User\User;
$PageName = 'Tag Products';
$PageSubfolder = '';
$MenuIndex = 1;

include('Shared/Header.php');

?>
<!-- Bootstrap -->
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<!-- Angular JS -->
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.min.js"></script>
<!-- Angular Animation -->
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular-animate.js"></script>
<link href="css/loyaltyProgram.css" rel="stylesheet" type="text/css" />
<script src="/js/loyaltyProgram.js" type="text/javascript"></script>


<div data-ng-app="tag-app" data-ng-controller="TagController" class="container">
    <br>

    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header" style="margin-top: 16px;">
                <span class="lp-logo">Loyalty Program</span>
            </div>
            <ul class="nav navbar-nav">
                <li ng-class="getClass('email_template.php')"><a  href="/email_template.php">Email Template</a></li>
                <li ng-class="getClass('loyalty_tiers.php')"><a  href="/loyalty_tiers.php">Loyalty Tiers</a></li>
                <li ng-class="getClass('loyalty_distribution.php')"><a  href="/loyalty_distribution.php">Points Distribution</a></li>
                <li ng-class="getClass('loyalty_rewards.php')"><a  href="/loyalty_rewards.php">Rewards</a></li>
            </ul>

            <ul class="nav navbar-nav navbar-left">
                <button class="btn btn-warning navbar-btn" id="app-disable" ng-style="{'background': products.app == true? 'orange' : 'grey'}" ng-bind="products.app == true? 'Disable application' : 'Enable application'" ng-click="appToggle()"></button>
            </ul>
        </div>
    </nav>
<div class="lp-content">
    <!-- Sign up bonus option-->
    <h1><small style="font-weight: bold;font-family: Arial, Helvetica, sans-serif;">Sign Up Bonus Options</small></h1>
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="" ng-checked="products.bonus.enable" ng-click="bonusToggle($event)">
        <label class="custom-control-label" for="defaultUnchecked">Gain Points For Sign up</label>
    </div>

    <div id = "tableCashless">
        <table id="item-table" class="table table-striped table-bordered table-sm" style="width: 500px">
            <thead>
            <tr>
                <th class="col-sm-3">
                    <b>Points</b>
                </th>
                <th class="col-sm-2">
                    <b>Email Template</b>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr ng-style="{'background':$odd?'#F2F4F4':'white'}">
                <td style="  text-transform:capitalize;">
                    <span>{{products.bonus.points}}</span>
                </td>
                <td>
                    <span>{{products.bonus.templateNameSelected}}</span>
                </td>
            </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-info btn-lg" style="background: #E7C362" data-toggle="modal" data-target="#modalBonus" ng-click="editBonus()"><span class="glyphicon glyphicon-edit"></span>Edit SignUp Bonus Option</button>
    </div>

    <!-- Cashless option -->
    <h1><small style="font-weight: bold;font-family: Arial, Helvetica, sans-serif;">Cashless Options</small></h1>
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="" ng-checked="products.cashless.enable" ng-click="cashlessToggle($event)">
        <label class="custom-control-label" for="defaultUnchecked">Gain Points Per Transaction</label>
    </div>

    <div id = "tableCashless">
        <table id="item-table" class="table table-striped table-bordered table-sm" style="width: 500px">
            <thead>
            <tr>
                <th class="col-sm-3">
                    <b>Points</b>
                </th>
                <th class="col-sm-2">
                    <b>Cash in cents</b>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr ng-style="{'background':$odd?'#F2F4F4':'white'}">
                <td style="  text-transform:capitalize;">
                    <span>{{products.cashless.points}}</span>
                </td>
                <td>
                    <span>{{products.cashless.cash}}</span>
                </td>
            </tr>
            </tbody>
        </table>
         <button type="button" class="btn btn-info btn-lg" style="background: #E7C362" data-toggle="modal" data-target="#modalCashless" ng-click="editCashless()"><span class="glyphicon glyphicon-edit"></span>Edit Cashless Option</button>
    </div>


    <h1><small style="font-weight: bold;font-family: Arial, Helvetica, sans-serif;">Points Distribution</small></h1>

    <table id="dis-table" class="table table-striped table-bordered table-sm" style="width: 600px">
    <thead>
        <tr>
            <th class="col-sm-2">
                <b>Access Point Scanner</b>
            </th>
            <th class="col-sm-1">
                <b>Points</b>
            </th>
            <th class="col-sm-2">
                <b>Limit Per Day</b>
            </th>
            <th class="col-sm-2">
                <b>Action</b>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr ng-style="{'background':$odd?'#F2F4F4':'white'}" data-ng-repeat="row in  products.points">
            <td class="acceeName" style="  text-transform:capitalize;">
                <span ng-show="true">{{row.access_point_name}}</span>
            </td>
            <td>
                <span ng-show="true">{{row.points}}</span>
            </td>
            <td>
                <span ng-show="true">{{row.limit_per_day}}</span>
            </td>
            <td>
                <button class="btn btn-primary btn-sm"  id="table-edit" ng-click="editDistribution(row, $index)" data-toggle="modal" data-target="#ModalDistribution"><span class="glyphicon glyphicon-edit"></span>Edit</button>
                <button class="btn btn-danger btn-sm"  id="table-delete" ng-click="initDelete(row,$index)" data-toggle="modal" data-target="#modalConfirmDelete"><span class="glyphicon glyphicon-trash"></span>Delete</button>
            </td>
        </tr>
    </tbody>
    </table>
    <button type="button" class="btn btn-info btn-lg" style="background: #E7C362" data-toggle="modal" data-target="{{distributionDisabled ==true?'#modalConfirm': '#ModalDistribution'}}"  ng-click="initForm()" ><span class="glyphicon glyphicon-plus"></span>Add Points Distribution</button>
    <!-- Trigger the modal with a button -->


    <!-- Modal Distribution -->
    <div id="ModalDistribution" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"  ng-bind="ModelHeader"></h4>
                </div>
                <div class="modal-body" style="height: 245px">
                    <form class="create--alert--form">
                        <div class="form-group" ng-show="!edit">
                            <label for="tag-name">Access Point Scanner</label>
                            <select ng-model="distribution.accessPointId" ng-click="displayAPList()">
                                <option value="">---None---</option>
                                <option ng-selected ="scanner.ID_Access_Point == distribution.accessPointId" ng-repeat="scanner in accessPointsList" value={{scanner.ID_Access_Point}}>
                                    {{scanner.Name}}
                                </option>
                            </select>
                        </div>

                         <div class="form-group" ng-show="edit">
                             <label for="tag-name">Access Point Scanner</label>                                                                                                            
                             <select ng-model="distribution.accessPointId" ng-disabled="disableSelection">                                                                                 
                                 <option value="">---None---</option>
                                 <option ng-selected ="scanner.ID_Access_Point == distribution.accessPointId" ng-repeat="scanner in products.accessList" value={{scanner.ID_Access_Point}}>
                                     {{scanner.Name}}                                                                                                                                      
                                 </option>                                                                                                                                                 
                             </select>                                                                                                                                                     
                             <small  id="scannerHelp" class="form-text text-muted">The Access Points Scanner can't be edited</small>
                         </div>                                                                                                                                                            


                          <div class="form-group">
                              <label for="dis-points">Points</label>
                              <input  class="form-control" id="dis-points" data-ng-model="distribution.points" ng-keyup="validPointsValue('dis-points')" onkeypress="return event.charCode >= 48 && event.charCode <= 57" aria-describedby="emailHelp" placeholder="Enter points" maxlength="10">
                          </div>

                          <div class="form-group">
                              <label for="dis-limit">Limit Per Day</label>
                              <input  class="form-control" id="dis-limit" data-ng-model="distribution.limit" ng-keyup="validLimitValue('dis-limit')" onkeypress="return event.charCode >= 48 && event.charCode <= 57" aria-describedby="emailHelp" placeholder="Enter points limit by day" maxlength="10">
                          </div>
                      </form>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-primary" ng-click="save()" ng-bind="action"></button>
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
              </div>
          </div>
      </div>

    <!-- Modal Bonus Options -->
    <div id="modalBonus" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Sign Up Bonus</h4>
                </div>
                <div class="modal-body" id="bonus-body">
                    <form class="update--bonus--form">
                        <div class="form-group">
                            <label for="cashless-points">Points</label>
                            <input  class="form-control" id="bonus-points" data-ng-model="bonus.points" ng-keyup="validCashlessPointsValue('cashless-points')" onkeypress="return event.charCode >= 48 && event.charCode <= 57" aria-describedby="emailHelp" placeholder="Enter points" maxlength="10">
                        </div>

                        <div class="form-group" >
                            <label for="tag-name">Email Template</label>
                            <select ng-model="bonus.templateId">
                               <!-- <option value="">---None---</option>  -->
                                <option ng-selected="template.id == bonus.templateId" ng-repeat="template in products.templates" value={{template.id}}>
                                    {{template.name}}
                                </option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" ng-click="updateBonus()">Update</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

      <!-- Modal Cashless Options -->
    <div id="modalCashless" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Cashless Options</h4>
                </div>
                <div class="modal-body" id="cashless-body">
                    <form class="update--cashless--form">
                        <div class="form-group">
                            <label for="cashless-points">Points</label>
                            <input  class="form-control" id="cashless-points" data-ng-model="cashless.points" ng-keyup="validCashlessPointsValue('cashless-points')" onkeypress="return event.charCode >= 48 && event.charCode <= 57" aria-describedby="emailHelp" placeholder="Enter points" maxlength="10">
                        </div>

                        <div class="form-group">
                            <label for="cashless-cash">Cash in cents</label>
                            <input  class="form-control" id="cashless-cash" data-ng-model="cashless.cash" ng-keyup="validCashlessCashValue('cashless-cash')" onkeypress="return event.charCode >= 48 && event.charCode <= 57" aria-describedby="emailHelp" placeholder="Enter cash" maxlength="10">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" ng-click="updateCashless()">Update</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <!--Modal of confirmation-->
    <div class="modal fade" id="modalConfirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-notify modal-info" role="document">
            <div class="modal-content text-center">
                <div class="modal-header" style="background:#00CED1">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Confirmation</h4>
                </div>
                <div class="modal-body">
                    <span class="glyphicon glyphicon-ban-circle fa-4x"></span>
                    <p>All the access points scanner have been distributed </p>

                </div>
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-default" style="background: #00CED1" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>



    <!--Modal of delete-->
    <div class="modal fade" id="modalConfirmDelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-notify modal-info" role="document">
            <div class="modal-content text-center">
                <div class="modal-header" style="background: red">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" ng-bind="distributionDeleteName"></h4>
                </div>
                <div class="modal-body">
                    <span class="glyphicon glyphicon-bell fa-4x"></span>
                    <p>Do you want to delete this cashless distribution ? </p>
                    <p> The associated data is also will be deleted</p>
                </div>
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-primary" style="background: red" ng-click="deleteDistribution(distributionDeleteId, indexDelete)">Delete</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>




<script type="text/javascript">

    let app = angular.module('tag-app',['ngAnimate']);

    app.controller('TagController',['$scope','$http','$location', function ($scope,$http,$location) {

        let init = function () {
            $scope.products = [];

            $http({
                method: 'GET',
                url: 'API.php?Action=GetAllPointsDistribution'
            }).then(function (result) {
                console.log(result);
                if(result.data.success){
                    $scope.products = result.data.products;
                }

                $scope.distributionDisabled = false;
                if($scope.products.accessList.length == $scope.products.points.length){
                    $scope.distributionDisabled = true;
                }
                console.log($scope.distributionDisabled);
            });


            $('body').append('');

        };

        $scope.getClass = function (path) {
            let str = $location.absUrl();
            let n = str.lastIndexOf('/');
            let result = str.substring(n + 1);

            return (result === path) ? 'active' : '';
        };


        $scope.validPointsValue = function (id) {
            let value =  document.getElementById(id).value;
            let reg = /^0/gi;
            if (value.match(reg)) {
                $scope.distribution.points ='';
            }
        };

        $scope.validLimitValue = function (id) {
            let value =  document.getElementById(id).value;
            let reg = /^0/gi;
            if (value.match(reg)) {
                $scope.distribution.limit ='';
            }
        };

        $scope.validCashlessPointsValue = function (id) {
            let value =  document.getElementById(id).value;
            let reg = /^0/gi;
            if (value.match(reg)) {
                $scope.cashless.points ='';
            }
        };

        $scope.validCashlessCashValue = function (id) {
            let value =  document.getElementById(id).value;
            let reg = /^0/gi;
            if (value.match(reg)) {
                $scope.cashless.cash ='';
            }
        };

        $scope.validEmail =function(x){
            let isEmail;
            let emails = x.split(",");
            isEmail =true;
            angular.forEach(emails, function (email) {
                if(!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.trim()))){
                    isEmail = false;
                }
            });
            return isEmail;
        };

        $scope.editBonus = function(){
            $scope.bonus = {};
            $scope.bonus.points = $scope.products.bonus.points;
            $scope.bonus.templateId = $scope.products.bonus.templateIdSelected;
        };

        $scope.updateBonus = function(){
            if($scope.bonus.points === ''){
                $scope.clearMessage();
                angular.element(document.querySelector( '#bonus-body' )).append('<div class="js-error-msg">The points can not be empty</div>');
                return;
            }
            if($scope.bonus.templateId === ''){
                $scope.clearMessage();
                angular.element(document.querySelector( '#bonus-body' )).append('<div class="js-error-msg">The template can not be empty</div>');
                return;}

            console.log($scope.bonus);
            $http.post('API.php?Action=UpdateBonusOption',$scope.bonus).then(function (result) {
                console.log(result);
                if(result.data){
                    $scope.products.bonus = angular.copy(result.data);
                } else {
                    $scope.products.bonus = angular.copy($scope.bonus);
                }
                $('#modalBonus').modal('hide');
            });
        };

        $scope.editCashless = function(){
            $scope.cashless = {};
            $scope.cashless.points = $scope.products.cashless.points;
            $scope.cashless.cash = $scope.products.cashless.cash;
        };

        $scope.updateCashless = function(){
            console.log($scope.cashless);
            if($scope.cashless.points === ''){
                $scope.clearMessage();
                angular.element(document.querySelector( '#cashless-body' )).append('<div class="js-error-msg">The points can not be empty</div>');
                return;
            }
            if($scope.cashless.cash === ''){
                $scope.clearMessage();
                angular.element(document.querySelector( '#cashless-body' )).append('<div class="js-error-msg">The cash can not be empty</div>');
                return;
            }
            $scope.cashless.enable= $scope.products.cashless.enable;

            $http.post('API.php?Action=UpdateCashlessOption',$scope.cashless).then(function (result) {
                if(result.status == 200){
                    $scope.products.cashless = angular.copy($scope.cashless);
                    $('#modalCashless').modal('hide');
                }
            });
        };

        $scope.distributionDisabled  = false;
        $scope.displayAPList = function(){

            $http({
                method: 'GET',
                url: 'API.php?Action=GetAvailableAccessPointsList'
            }).then(function (result) {
                console.log(result);
                if(result.data.success){
                    console.log(result.data.products);
                    $scope.accessPointsList= result.data.products;

                    console.log($scope.accessPointsList.length);
                    console.log($scope.products.accessList.length);
                }

            });
        };


        $scope.distribution = {};
        $scope.initForm = function () {
            $scope.clearMessage();
            $scope.accessPointsList = '';
            $scope.ModelHeader = 'Add Point Distribution';
            $scope.action= 'Save';

            $scope.distribution.accessPointId = '';
            $scope.distribution.name = '';
            $scope.distribution.points = '';
            $scope.distribution.limit = '';
            $scope.edit = false;
            $scope.disableSelection = false; 
        };

        $scope.edit = false;
        $scope.disableSelection = false; 
        $scope.editDistribution = function (row,index) {
            $scope.clearMessage();
            $scope.ModelHeader = 'Edit Point Distribution';
            $scope.action= 'Update';
            $scope.distribution.id = row.id;
            $scope.distribution.accessPointId = row.access_point_id;
            $scope.distribution.name = row.access_point_name;
        

            $scope.distribution.points = row.points;
            $scope.distribution.limit = row.limit_per_day;
            $scope.selectedId = row.access_point_id;
            $scope.edit = true;
            $scope.disableSelection = true;
            $scope.index = index;
        };

        $scope.initDelete = function(row,index){
            $scope.distributionDeleteId = row.id;
            $scope.distributionDeleteName = 'Delete the cashless distribution ['+row.access_point_name+']' ;
            $scope.indexDelete = index;
        };

        $scope.save = function() {
            console.log($scope.edit);
            console.log($scope.distribution);
            if($scope.distribution.points === ''){$scope.showAlert('Please input the points'); return;}
            if($scope.distribution.limit === ''){$scope.showAlert('Please input limit par day'); return;}


            if($scope.edit === false){
                if($scope.distribution.accessPointId === ''){$scope.showAlert('Please select the distribution name'); return;}

                $http.post('API.php?Action=CreatePointsDistribution', $scope.distribution).then(function (result) {
                    console.log(result.data);
                    if (result.data.error) {
                        $scope.showError(result.data.error);
                    } else {
                        $scope.products.points.push(result.data);
                        if($scope.products.accessList.length == $scope.products.points.length){
                            $scope.distributionDisabled = true;
                        }
                        $('#ModalDistribution').modal('hide');
                    }
                });
            } else {
               // $scope.distribution.accessPointId = $scope.selectedId;
                $http.post('API.php?Action=UpdatePointsDistribution',$scope.distribution).then(function (result) {
                    console.log(result.data);
                      if (result.data.error) {
                          $scope.showError(result.data.error);
                      } else {
                          $scope.products.points[$scope.index] = angular.copy(result.data);
                          $('#ModalDistribution').modal('hide');
                      }
                });
            }
        };

        $scope.deleteDistribution = function (id,index) {
            let data = {
                distributionId: id
            };

            $scope.products.points.splice(index, 1);
            $http.post('API.php?Action=DeletePointsDistribution',data).then(function (result) {
                console.log(result);
                $scope.distributionDisabled  = false;
                $('#modalConfirmDelete').modal('hide');
            });
        };

        $scope.getAccessPointScanner = function () {
            $http.get('API.php?Action=GetAllScanners').then(function (result) {
                console.log(result);
                if(result.data.success){
                    $scope.scanners = result.data.products;
                }
            });
        };

        $scope.bonusToggle = function(event){

            let checked = event.target.checked;
            let data = {
                status: checked
            };
            console.log(data);
            $http.post('API.php?Action=UpdateBonusStatus',data).then(function (result) {
                console.log(result);
                init();
            });
        };

        $scope.cashlessToggle = function(event){

            let checked = event.target.checked;
            let data = {
                status: checked
            };
            console.log(data);
            $http.post('API.php?Action=UpdateCashlessStatus',data).then(function (result) {
                console.log(result);
                init();
            });
        };

        $scope.appToggle = function(){

            let btnText =$('#app-disable').html();
            let checked = true;
            $('#app-disable').html('Disable application');
            $('#app-disable').css("background-color", "orange");

            if(btnText === 'Disable application'){
                $('#app-disable').html('Enable application');
                $('#app-disable').css("background-color", "grey");
                checked = false;
            }
            console.log(checked);
            let data = {
                status: checked
            };

            $http.post('API.php?Action=UpdateAppStatus',data).then(function (result) {
                console.log(result);
                if(result.status == 200){
                    init();
                }
            });
        };

        $scope.clearMessage = function(){
            angular.element(document.querySelector( '.js-error-msg' )).remove();
        };

        $scope.showAlert = function(alertMsg){
            angular.element(document.querySelector( '.js-error-msg' )).remove();
            angular.element(document.querySelector( '.modal-body' )).append('<div class="js-error-msg">' +alertMsg+ '</div>');
        };

        $scope.showError = function (errorMsg){
            angular.element(document.querySelector( '.js-error-msg' )).remove();
            angular.element(document.querySelector( '.modal-body' )).append('<div class="js-error-msg">' +errorMsg+ '</div>');
        };
        init();
    }]);



</script>


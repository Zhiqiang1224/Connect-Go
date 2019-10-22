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
    <h1><small class="label-table">Rewards</small></h1>

    <table id="item-table" class="table table-striped table-bordered table-sm" ">
    <thead>
    <tr>
        <th class="col-sm-1">
            <b>Points</b>
            <span ng-click="orderByField='points'; reverseSort = !reverseSort">
                    <i class="fa fa-fw fa-sort" ng-show="!reverseSort"></i>
                    <i class="fa fa-fw fa-sort" ng-show="reverseSort"></i>
                </span>
        </th>
        <th class="col-sm-2">
            <b>Rewards (add-on)</b>
            <span ng-click="orderByField='add_on_name'; reverseSort = !reverseSort">
                    <i class="fa fa-fw fa-sort" ng-show="!reverseSort"></i>
                    <i class="fa fa-fw fa-sort" ng-show="reverseSort"></i>
                </span>
        </th>
        <th class="col-sm-2">
            <b>Email Template</b>
            <span ng-click="orderByField='email_name'; reverseSort = !reverseSort">
                    <i class="fa fa-fw fa-sort" ng-show="!reverseSort"></i>
                    <i class="fa fa-fw fa-sort" ng-show="reverseSort"></i>
                </span>
        </th>
        <th class="col-sm-2">
            <b>Destination email</b>
            <span ng-click="orderByField='destination_email'; reverseSort = !reverseSort">
                    <i class="fa fa-fw fa-sort" ng-show="!reverseSort"></i>
                    <i class="fa fa-fw fa-sort" ng-show="reverseSort"></i>
                </span>
        </th>
        <th class="col-sm-2">
            <b>Action</b>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr ng-style="{'background':$odd?'#F2F4F4':'white'}" data-ng-repeat="row in  products.rewards  | orderBy: orderByField:reverseSort">
        <td>
            <span>{{row.points}}</span>
        </td>
        <td>
            <span>{{row.add_on_name}}</span>
        </td>
        <td>
            <span>{{row.email_name}}</span>
        </td>
        <td>
            <span class="email-wrap">{{row.destination_email}}</span>
        </td>
        <td>
            <button class="btn btn-primary btn-sm"  id="table-edit" ng-click="editReward(row, $index)" data-toggle="modal" data-target="#ModalRewards"><span class="glyphicon glyphicon-edit"></span>Edit</button>
            <button class="btn btn-danger btn-sm"  id="table-delete" ng-click="initDelete(row)" data-toggle="modal" data-target="#modalConfirmDelete"><span class="glyphicon glyphicon-trash"></span>Delete</button>
        </td>
    </tr>
    </tbody>
    </table>


    <button type="button" class="btn btn-info btn-lg" style="background: #E7C362" data-toggle="modal" data-target="#ModalRewards" ng-click="initForm()">Add Rewards</button>
    <!-- Trigger the modal with a button -->


    <!-- Modal -->
    <div id="ModalRewards" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" ng-bind="ModelHeader"></h4>
                </div>
                <div class="modal-body" style="height:348px;">
                    <form class="create--alert--form">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Points</label>
                            <input  class="form-control" id="rewards-points" maxlength="10" data-ng-model="rewards.points" ng-keyup="validPointsValue('rewards-points')" onkeypress="return event.charCode >= 48 && event.charCode <= 57" aria-describedby="emailHelp" placeholder="Enter points">

                        </div>

                        <div class="form-group">
                                <label for="tag-name">Rewards (add-on)</label>
                                <input type="checkbox" class="custom-control-input" id="addOnChecked" ng-checked="addOnChecked" ng-click="addOnToggle(addOnChecked)">
                                <label for="">Disable</label>

                            <select ng-model="rewards.addOnId" ng-disabled="addOnChecked">
                                <option value="">---None---</option>
                                <option ng-selected="addOn.id == rewards.addOnId" ng-repeat="addOn in products.addOns" value={{addOn.id}}>
                                    {{addOn.name}}
                                </option>
                            </select>
                            <small id="emailHelp" class="form-text text-muted">Check the  chekbox will disable the add on .</small>
                        </div>

                        <div class="form-group">
                            <label for="tag-name">Destination email</label>
                            <input type="checkbox" class="custom-control-input" id="patronChecked" ng-checked="patronChecked" ng-click="patronToggle(patronChecked)" ng-model="rewards.patron" >
                            <label for="">Patron</label>

                            <input  id="des-email" class="form-control"  ng-model="rewards.email"  ng-disabled="patronChecked" placeholder="Email">
                            <small id="emailHelp" class="form-text text-muted">Use the Patron checkbox option above to enable or disable the email destination.</small>
                        </div>

                        <div class="form-group" >
                            <label for="tag-name">Email Template</label>
                            <select ng-model="rewards.templateId">
                                <option value="">---None---</option>
                                <option ng-selected="template.id == rewards.templateId" ng-repeat="template in products.templates" value={{template.id}}>
                                    {{template.name}}
                                </option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" ng-click="saveRewards()" ng-bind="action"></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

        <div class="modal fade" id="modalConfirmDelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-notify modal-info" role="document">
                <div class="modal-content text-center">
                    <div class="modal-header" style="background: red">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" ng-bind="rewardsDeleteName"></h4>
                    </div>
                    <div class="modal-body">
                        <span class="glyphicon glyphicon-bell fa-4x"></span>
                        <p>Do you want to delete this reward ? </p>
                        <p> The associated data is also will be deleted</p>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-primary" style="background: red" ng-click="deleteReward(rewardDeleteId, indexDelete)">Delete</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalConfirmEmpty" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-notify modal-info" role="document">
                <div class="modal-content text-center">
                    <div class="modal-header" style="background:#00CED1">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Confirmation</h4>
                    </div>
                    <div class="modal-body">
                        <span class="glyphicon glyphicon-info-sign fa-4x"></span>
                        <p>There are no loyalty rewards</p>

                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-default" style="background: #00CED1" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<script type="text/javascript">

    var app = angular.module('tag-app',['ngAnimate']);

    app.controller('TagController',['$scope','$http','$location', function ($scope,$http,$location) {

        var init = function () {
            $scope.products = [];

            $http({
                method: 'GET',
                url: 'API.php?Action=GetAllRewards'
            }).then(function (result) {
                console.log(result);
                if(result.data.success){
                    $scope.products = result.data.products;
                }
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
                $scope.rewards.points ='';
            }
        };

        $scope.validEmail =function(x){
            let isEmail;
            let emails = x.split(";");
            isEmail =true;
            angular.forEach(emails, function (email) {
                if(!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.trim()))){
                    isEmail = false;
                }
            });
            return isEmail;
        };

        $scope.rewards = {};
        $scope.initForm = function () {
            $scope.edit = false;
            $scope.clearMessage();
            $scope.ModelHeader = 'Add Rewards';
            $scope.action= 'Save';

            $scope.rewards.points = '';
            $scope.rewards.email = '';

            $scope.rewards.addOnId = '';
            $scope.rewards.templateId = '';

            $scope.rewards.patron = true;

            $scope.patronChecked = true;
            $scope.addOnChecked = false;

        };

        $scope.edit = false;
        $scope.hideOption = true;
        $scope.editReward = function (row,index) {
            $scope.edit = true;
            $scope.hideOption = false;
            $scope.clearMessage();
            $scope.ModelHeader = 'Edit Rewards';
            $scope.action= 'Update';

            $scope.rewards.id = row.id;

            $scope.rewards.points = row.points;
            $scope.rewards.email = (row.destination_email === 'Patron')? '': row.destination_email;

            $scope.rewards.addOnId = row.add_on_field_id;
            $scope.rewards.templateId = row.email_template_id;

            $scope.rewards.patron = (row.patron_email_flag == 1)? true : false;
            $scope.patronChecked = (row.patron_email_flag == 1)? true : false;
            $scope.addOnChecked = (row.add_on_field_id === null)? true : false;

            if(row.add_on_name){
                $scope.addOnChecked = false;
            } else {
                $scope.addOnChecked = true;
            }

            $scope.index = index;
        };

        $scope.initDelete = function(row){
            $scope.rewardDeleteId = row.id;
            $scope.rewardsDeleteName = 'Delete the reward ['+row.add_on_name+']' ;
            $scope.indexDelete = $scope.products.rewards.indexOf(row);
        };


        $scope.saveRewards = function() {
            console.log($scope.edit);
            console.log($scope.rewards);

            if($scope.rewards.points === ''){$scope.showAlert('Please Input The Points'); return;}

            if($scope.rewards.patron  === true){
                $scope.rewards.patron = 1;
                $scope.rewards.email = '';
            } else {
                $scope.rewards.patron = 0;
            }

            let checkEmail= $scope.validEmail($scope.rewards.email);
            if($scope.rewards.email){
                if(!checkEmail)  {
                    $scope.showError('The Email Is Not Valid');
                    return;
                }
            }

            if($scope.edit === false){
                $http.post('API.php?Action=CreateRewards', $scope.rewards).then(function (result) {
                    console.log(result.data);
                    if (result.data.error) {
                        $scope.showError(result.data.error);
                    } else {
                        $scope.products.rewards.push(result.data);
                        $('#ModalRewards').modal('hide');
                        //init();
                    }
                });
            } else {
                $http.post('API.php?Action=UpdateRewards',$scope.rewards).then(function (result) {
                    console.log(result.data);
                    if (result.data.error) {
                        $scope.showError(result.data.error);
                    } else {

                        $scope.products.rewards[$scope.index] = angular.copy(result.data);
                        $('#ModalRewards').modal('hide');
                       // init();
                    }
                });
            }

        };

        $scope.deleteReward = function (id,index) {
            let data = {
                rewardId: id
            };

            $scope.products.rewards.splice(index, 1);
            $http.post('API.php?Action=DeleteRewards',data).then(function (result) {
                console.log(result);
                $('#modalConfirmDelete').modal('hide');
            });
        };

        $scope.addOnToggle = function(state){
            if(state === true){
                $scope.addOnChecked = false;
                $scope.addOnSelected = '---None---';
            } else {
                $scope.addOnChecked = true;
                $scope.addOnSelected = '';
                $scope.rewards.addOnId = '';
            }
        };

        $scope.patronToggle = function(state){
            if(state === true){
                $scope.patronChecked = false;

            } else{
                $scope.patronChecked = true;
                $scope.rewards.email = '';

            }
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

    app.filter('orderBy', function() {
        return function(items, field, reverse) {
            let filtered = [];
            angular.forEach(items, function(item) {
                filtered.push(item);
            });
            filtered.sort(function (a, b) {
                return (a[field] > b[field] ? -1 : 1);
            });
            if(reverse) filtered.reverse();
            return filtered;
        };
    });


</script>


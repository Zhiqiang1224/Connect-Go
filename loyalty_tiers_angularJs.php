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
    <h1><small class="label-table">Loyalty   Tiers</small></h1>

    <table id="item-table" class="table table-striped table-bordered table-sm">
        <thead>
        <tr>
            <th class="col-sm-3">
                <b>Tier Name</b>
                <span ng-click="orderByField='name'; reverseSort = !reverseSort">
                    <i class="fa fa-fw fa-sort" ng-show="!reverseSort"></i>
                    <i class="fa fa-fw fa-sort" ng-show="reverseSort"></i>
                </span>
            </th>
            <th class="col-sm-2">
                <b>Pointd Required</b>
                <span ng-click="orderByField='points_required'; reverseSort = !reverseSort">
                    <i class="fa fa-fw fa-sort" ng-show="!reverseSort"></i>
                    <i class="fa fa-fw fa-sort" ng-show="reverseSort"></i>
                </span>
            </th>
            <th class="col-sm-2">
                <b>Email Template</b>
                <span ng-click="orderByField='email_template_name'; reverseSort = !reverseSort">
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
        <tr ng-style="{'background':$odd?'#F2F4F4':'white'}" data-ng-repeat="row in  products.tiers  | orderBy: orderByField:reverseSort">
            <td>
                <span>{{row.name}}</span>
            </td>
            <td>
                <span>{{row.points_required}}</span>
            </td>
            <td>
                <span>{{row.email_template_name}}</span>
            </td>
            <td>
                <button class="btn btn-primary btn-sm"  id="table-edit" ng-click="editTier(row, $index)" data-toggle="modal" data-target="#ModalTier" ><span class="glyphicon glyphicon-edit"></span>Edit</button>
                <button class="btn btn-danger btn-sm"  id="table-delete" ng-click="initDelete(row)" data-toggle="modal" data-target="#modalConfirmDelete"><span class="glyphicon glyphicon-trash"></span>Delete</button>
            </td>
        </tr>
        </tbody>
    </table>


    <button type="button" class="btn btn-info btn-lg" style="background: #E7C362" data-toggle="modal" data-target="#ModalTier" ng-click="initForm()">Add Loyalty Tier</button>
    <!-- Modal -->
    <div id="ModalTier" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" ng-bind="ModelHeader"></h4>
                </div>
                <div class="modal-body">
                    <form class="create--tier--form">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Tier Name</label>
                            <input class="form-control" id="tier-name" data-ng-model="tier.name" aria-describedby="emailHelp" placeholder="Template name">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">Points</label>
                            <input  class="form-control" id="tier-points" data-ng-model="tier.points" ng-keyup="validValue()" onkeypress="return event.charCode >= 48 && event.charCode <= 57" aria-describedby="emailHelp" placeholder="Enter points">
                            <small id="pointsHelp" class="form-text text-muted">Only number is allowed to input.</small>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">Email Template</label>
                            <select ng-model="tier.templateId" >
                                <option  value="">---None---</option>
                                <option ng-selected="template.id == tier.templateId" ng-repeat="template in products.templates" value={{template.id}}>
                                    {{template.name}}
                                </option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"  ng-click="saveTier()" ng-bind="action"></button>
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
                        <h4 class="modal-title" ng-bind="tierDeleteName"></h4>
                    </div>
                    <div class="modal-body">
                        <span class="glyphicon glyphicon-bell fa-4x"></span>
                        <p>Do you want to delete this tier ? </p>
                        <p> The associated data is also will be deleted</p>

                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-primary" style="background: red" ng-click="deleteTier(tierDeleteId, indexDelete)">Delete</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!--Modal of empty record-->
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
                        <p>There is no loyalty tier </p>
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

    let app = angular.module('tag-app',['ngAnimate']);

    app.controller('TagController',['$scope','$http','$location', function ($scope,$http,$location) {

        let init = function () {
            $scope.products = [];

            $http({
                method: 'GET',
                url: 'API.php?Action=GetAllTiers'
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

        $scope.validValue = function () {
            let value =  document.getElementById('tier-points').value;
            let reg =  /^0\d{1}/;
            if (value.match(reg)) {
                $scope.tier.points ='';
            }
        };

        $scope.checkTierName = function(inputName) {
            let existName = false;
            angular.forEach($scope.products.tiers, function (tier) {
                if(tier.name == inputName){
                    existName = true;
                }
            });
            return existName;
        };


        $scope.tier = {};
        $scope.initForm = function () {
            $scope.clearMessage();
            $scope.ModelHeader = 'Add Loyalty Tier';
            $scope.action= 'Save';
            $scope.tier.id = '';
            $scope.tier.name = '';
            $scope.tier.points = '';
            $scope.tier.templateId = '';
        };

        $scope.edit = false;
        $scope.editTier = function (row,index) {
            $scope.clearMessage();
            $scope.ModelHeader = 'Edit Loyalty Tier';
            $scope.action= 'Update';
            $scope.tier = {};
            $scope.tier.id = row.id;
            $scope.tier.templateId = row.email_template_id;
            $scope.tier.name = row.name;
            $scope.tier.points = row.points_required;
            $scope.edit = true;
            $scope.index = index;
        };

        $scope.initDelete = function(row){
            $scope.tierDeleteId = row.id;
            $scope.tierDeleteName = 'Delete the tier ['+row.name+']' ;
            $scope.indexDelete = $scope.products.tiers.indexOf(row);
        };


        $scope.saveTier = function() {
            if($scope.tier.name === ''){$scope.showAlert('Please input the tier name'); return;}
            if($scope.tier.points === ''){$scope.showAlert('Please input the points'); return;}
           // if($scope.tier.templateId === ''){$scope.showAlert('Please select the email template'); return;}

            console.log($scope.tier);
            if($scope.edit === false){
                if($scope.checkTierName($scope.tier.name)){$scope.showAlert('The tier name is already exist'); return;}

                $http.post('API.php?Action=CreateTier', $scope.tier).then(function (result) {
                    console.log(result.data);
                    if (result.data.error) {
                        $scope.showError(result.data.error);
                    } else {
                        $scope.products.tiers.push(result.data);
                        $('#ModalTier').modal('hide');
                    }
                });
            } else {
                $http.post('API.php?Action=UpdateTier',$scope.tier).then(function (result) {
                    console.log(result.data);
                    $scope.products.tiers[$scope.index] = angular.copy(result.data);
                    $('#ModalTier').modal('hide');

                });
            }

        };



        $scope.deleteTier = function (id,index) {
            let data = {
                tierId: id
            };

            $scope.products.tiers.splice(index, 1);
            $http.post('API.php?Action=DeleteTier',data).then(function (result) {
                console.log(result);
                $('#modalConfirmDelete').modal('hide');
            });
        };

        $scope.getEmailTemplate = function () {
            $http.get('API.php?Action=GetAllTemplates').then(function (result) {
                console.log(result);
                if(result.data.success){
                    $scope.templates = result.data.products;
                }
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


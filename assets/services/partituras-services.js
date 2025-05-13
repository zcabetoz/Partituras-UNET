import angular from 'angular';
import 'angular-animate';
import 'angularjs-toaster';

angular.module('partituras.services', ['ngAnimate', 'toaster'])
    .service('PartiturasServices', function (toaster, $timeout) {
        let $ctrl = this;

        $ctrl.toasterMessageService = function (mensaje, type, time = 5000) {
            toaster.pop({
                type: type,
                body: mensaje,
                timeout: time,
                showCloseButton: true
            });
        }

        $ctrl.elementFocus = function (element) {
            let input = $('#' + element);
            $timeout(function () {
                input.focus();
                input[0].setSelectionRange(input.val().length, input.val().length);
            }, 100);
        }

        $ctrl.validField = function (fieldName) {
            let field = $scope.formRegisterRole[fieldName];
            return field.$invalid && field.$dirty || $ctrl.campoError === fieldName && field.$invalid
        };
    })

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
                showCloseButton: true,
            });
        }

        $ctrl.elementFocus = function (element) {
            let input = $('#' + element);
            $timeout(function () {
                input.focus();
                input[0].setSelectionRange(input.val().length, input.val().length);
            }, 50);
        }

        $ctrl.hasError = function (form, fieldName, campoError, extraCondition = false) {
            let field = form[fieldName];
            return field.$invalid && field.$dirty || campoError === fieldName && field.$invalid || extraCondition;
        };
    })
    .run(function ($timeout) {
        $(document).on('hide.bs.modal', function (e) {
            if (document.activeElement) {
                document.activeElement.blur();
            }
        });
    });




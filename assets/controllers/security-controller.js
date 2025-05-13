import Routing from 'fos-router';

export default function SecurityController($scope, $http, PartiturasServices, $timeout) {
    let $ctrl = this;

    $ctrl.options = {};
    $ctrl.totalItems = 0;
    $ctrl.currentPage = 1;
    $ctrl.numItems = 15;

    $ctrl.$onInit = function () {
        $ctrl.getRoles();
        $ctrl.isOpenMdlDeleteRole = false;
        $ctrl.rol = null;
    }

    $ctrl.setPage = function (pageNo) {
        $ctrl.currentPage = pageNo;
    };

    $ctrl.pageChanged = function () {
        $ctrl.getRoles();
    };

    $ctrl.setNumItems = function () {
        $ctrl.currentPage = 1;
        $ctrl.getRoles();
    };

    $ctrl.getRoles = function () {
        let url = Routing.generate('security_list_roles_tbl');

        $ctrl.isLoading = true;

        $ctrl.options.skip = $ctrl.currentPage;
        $ctrl.options.limit = $ctrl.numItems;

        $http.post(url, $ctrl.options).then(function (response) {
            $ctrl.roles = response.data.data;
            $ctrl.totalItems = response.data.count;
            $ctrl.isLoading = false;
        });
    }

    $ctrl.openMdlDeleteRole = function (role) {
        $ctrl.role = role.role;
        $ctrl.description = role.description;
        $ctrl.idRoleDelete = role.id;
        angular.element($('#mdl-delete-role').modal('show'));
    }

    $ctrl.deleteRole = function () {
        $ctrl.isLoadingModal = true;
        let url = Routing.generate('security_delete_role');

        $http.delete(url, {data: {id: $ctrl.idRoleDelete}}).then(function (response) {
            $ctrl.isLoadingModal = false;
            angular.element($('#mdl-delete-role').modal('hide'));
            PartiturasServices.toasterMessageService('Rol eliminado correctamente', 'success');
            $ctrl.getRoles();
        });
    }

    $ctrl.openMdlRegisterRole = function (role = null) {
        $ctrl.rol = role;
        $ctrl.role = role ? role.role : '';
        $ctrl.description = role ? role.description : '';


        $ctrl.titleMdlRegisterRole = role ? 'Editar Rol' : 'Registrar Rol';
        angular.element($('#mdl-register-role').modal('show'));

        $timeout(function () {
            if ($scope.formRegisterRole) {
                $scope.formRegisterRole.$setPristine();
                $scope.formRegisterRole.$setUntouched();
            }

            $ctrl.descriptionError =  $ctrl.roleError = false;

            PartiturasServices.elementFocus('user_role_role');
        }, 100);
    }

    $ctrl.registerRole = function ($event) {
        $event.preventDefault();

        if ($scope.formRegisterRole.$valid) {
            $ctrl.isLoadingModal = true;
            let url = Routing.generate('security_register_role');

            let data = {
                'role': $ctrl.role.trim().toUpperCase(),
                'description': $ctrl.description.trim(),
                'id': $ctrl.rol?.id ?? null
            }

            $http.post(url, data).then(function (response) {
                response = response.data;
                $ctrl.isLoadingModal = false;
                if(response.status === 'error'){
                    PartiturasServices.toasterMessageService(response.message, response.status);
                    $ctrl.roleError = true;
                    PartiturasServices.elementFocus('user_role_role');
                }else{
                    angular.element($('#mdl-register-role').modal('hide'));
                    PartiturasServices.toasterMessageService(response.message, response.status);
                    $ctrl.getRoles();
                }

            });
        } else {
            const requiredErrors = $scope.formRegisterRole.$error['required'];

            if (requiredErrors && requiredErrors.length > 0) {
                const invalidField = requiredErrors[0];
                $ctrl.campoError = invalidField.$name;
                $ctrl.validateFields();
                PartiturasServices.elementFocus(invalidField.$$element?.attr('id'));
            }
        }
    }

    $ctrl.validateFields = function () {
        $ctrl.roleError = $ctrl.hasError('user_role[role]');
        $ctrl.descriptionError = $ctrl.hasError('user_role[description]');
    };

    $ctrl.hasError = function (fieldName) {
        let field = $scope.formRegisterRole[fieldName];
        return field.$invalid && field.$dirty || $ctrl.campoError === fieldName && field.$invalid;
    };
}
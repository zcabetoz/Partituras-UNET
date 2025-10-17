import Routing from 'fos-router';

export default function RoleController($scope, $http, PartiturasServices) {
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
        angular.element('#mdl-delete-role').modal('show');
    }

    $ctrl.deleteRole = function () {
        $ctrl.isLoadingModal = true;
        let url = Routing.generate('security_delete_role');

        $http.delete(url, {data: {id: $ctrl.idRoleDelete}}).then(function (response) {
            $ctrl.isLoadingModal = false;
            angular.element('#mdl-delete-role').modal('hide');
            PartiturasServices.toasterMessageService('Rol eliminado correctamente', 'success');
            $ctrl.getRoles();
        });
    }

    $ctrl.openMdlRegisterRole = function (role = null) {
        const mdlRegisterRole = angular.element('#mdl-register-role');
        $ctrl.campoError = null;
        $ctrl.rol = role;
        $ctrl.role = role?.role ?? '';
        $ctrl.description = role?.description ?? '';
        $ctrl.descriptionError = $ctrl.roleError = false;

        $ctrl.titleMdlRegisterRole = role ? 'Editar Rol' : 'Registrar Rol';

        mdlRegisterRole.modal('show');

        mdlRegisterRole.off('shown.bs.modal');

        mdlRegisterRole.on('shown.bs.modal', function () {
            $scope.formRegisterRole['user_role[description]'].$setPristine();
            PartiturasServices.elementFocus('user_role_role');
        });
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

                PartiturasServices.toasterMessageService(response.message, response.status);

                if (response.status === 'error') {
                    $ctrl.roleError = true;
                    PartiturasServices.elementFocus('user_role_role');
                } else {
                    angular.element('#mdl-register-role').modal('hide');
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
        $ctrl.roleError = PartiturasServices.hasErrorService($scope.formRegisterRole, 'user_role[role]', $ctrl.campoError);
        $ctrl.descriptionError = PartiturasServices.hasErrorService($scope.formRegisterRole, 'user_role[description]', $ctrl.campoError);
    };
}
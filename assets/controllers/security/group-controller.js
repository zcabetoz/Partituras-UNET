import Routing from "fos-router";

export default function GroupController($scope, $http, PartiturasServices) {
    let $ctrl = this;

    $ctrl.$onInit = function () {
        $ctrl.groups = [];
        $ctrl.getGroups();
    }

    $ctrl.getGroups = function (action = 'load') {
        $ctrl.isLoadingGroups = true;
        let url = Routing.generate('security_list_groups_tbl');

        $http.get(url).then(function (response) {
            $ctrl.isLoadingGroups = false;
            $ctrl.groups = response.data;
            let index = 0;
            if (action === 'new') index = $ctrl.groups.length - 1;
            if (action === 'edit') index = $ctrl.groups.findIndex(group => group.id === $ctrl.group.id);

            $ctrl.groupSelected = $ctrl.groups[index];

            if ($ctrl.groups.length > 0) {
                $ctrl.getRolesGroup($ctrl.groupSelected);
            }
        });
    }

    $ctrl.getRolesGroup = function (group, modal = false) {
        if (modal) angular.element('#mdl-add-roles').modal('hide');

        $ctrl.loadingRolesFromGroup = true;

        $ctrl.getRolesFromGroup(group, false).then(function (data) {
            $ctrl.rolesFromGroup = data
            $ctrl.loadingRolesFromGroup = false
        });
    }

    $ctrl.getRolesFromGroup = function (group, exclude) {
        let url = Routing.generate('security_get_roles_from_group', {id: group.id, exclude: exclude});

        return $http.get(url).then(function (response) {
            return response.data;
        });
    }

    $ctrl.openMdlDeleteGroup = function (group) {
        $ctrl.nameGroupDelete = group.nombre;
        $ctrl.idGroupDelete = group.id;
        angular.element('#mdl-delete-group').modal('show');
    }

    $ctrl.deleteGroup = function () {
        $ctrl.isLoadingModal = true;
        let url = Routing.generate('security_delete_group');

        $http.delete(url, {data: {id: $ctrl.idGroupDelete}}).then(function (response) {
            $ctrl.isLoadingModal = false;
            angular.element('#mdl-delete-group').modal('hide');
            PartiturasServices.toasterMessageService('Grupo eliminado correctamente', 'success');
            $ctrl.getGroups();
        });
    }

    $ctrl.openMdlRegisterGroup = function (group = null) {
        const mdlRegisterGroup = angular.element('#mdl-register-group');
        $ctrl.groupError = false;

        $ctrl.group = group;

        $('form[name="formRegisterGroup"]').trigger('reset');

        $ctrl.nameGroup = group?.nombre;

        $ctrl.titleMdlRegisterGroup = group ? 'Modificar' : 'Registrar';

        mdlRegisterGroup.modal('show');

        mdlRegisterGroup.on('shown.bs.modal', function () {
            $scope.formRegisterGroup.$setPristine();
            $scope.formRegisterGroup.$setUntouched();

            PartiturasServices.elementFocus('user_group_name');
        });
    }

    $ctrl.registerGroup = function ($event) {
        $event.preventDefault();

        if ($scope.formRegisterGroup.$valid) {
            $ctrl.isLoadingModal = true;
            let url = Routing.generate('security_register_group');

            let data = {
                'nameGroup': $ctrl.nameGroup.trim().toUpperCase(),
                'id': $ctrl.group?.id ?? null
            }

            $http.post(url, data).then(function (response) {
                response = response.data;
                $ctrl.isLoadingModal = false;
                if (response.status === 'error') {
                    PartiturasServices.toasterMessageService(response.message, response.status);
                    $ctrl.groupError = true;
                    PartiturasServices.elementFocus('user_group_name');
                } else {
                    angular.element('#mdl-register-group').modal('hide');
                    PartiturasServices.toasterMessageService(response.message, response.status);
                    $ctrl.getGroups($ctrl.group ? 'edit' : 'new');
                }
            });
        } else {
            const requiredErrors = $scope.formRegisterGroup.$error['required'];

            if (requiredErrors && requiredErrors.length > 0) {
                const invalidField = requiredErrors[0];
                $ctrl.campoError = invalidField.$name;
                $ctrl.validateFields();
                PartiturasServices.elementFocus(invalidField.$$element?.attr('id'));
            }
        }

    }

    $ctrl.openMdlAddRoles = function (group) {
        const mdlAddRoles = angular.element('#mdl-add-roles');
        $ctrl.loadingRolesNotGroup = true;

        mdlAddRoles.modal('show');

        $ctrl.getRolesFromGroup(group, true).then(function (data) {
            $ctrl.rolesNotGroup = data;
            $ctrl.loadingRolesNotGroup = false;
        })
    }

    $ctrl.validateFields = function () {
        $ctrl.groupError = PartiturasServices.hasError($scope.formRegisterGroup, 'user_group[name]', $ctrl.campoError);
    };

    $ctrl.checkRoleGroup = function (idRole, view = false) {
        let url = Routing.generate('security_check_role_group');

        $http.post(url, {idRole: idRole, idGroup: $ctrl.groupSelected.id}).then(function (){
            if(view) $ctrl.getRolesGroup($ctrl.groupSelected);
        })
    }
}
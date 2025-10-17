import Routing from 'fos-router';

export default function UserController($scope, $http, PartiturasServices, $timeout) {
    let $ctrl = this;
    let form;

    $ctrl.options = {};
    $ctrl.totalItems = 0;
    $ctrl.currentPage = 1;
    $ctrl.numItems = 15;

    $ctrl.setPage = (pageNo) => {
        $ctrl.currentPage = pageNo;
    };

    $ctrl.pageChanged = () => {
        $ctrl.getUsers();
    };

    $ctrl.setNumItems = () => {
        $ctrl.currentPage = 1;
        $ctrl.getUsers();
    };

    $ctrl.$onInit = () => {
        $ctrl.search = $ctrl.campoError = '';
        $ctrl.users = [];
        $ctrl.userEdit = {};
        $ctrl.regexPassword = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_])\S{10,20}$/;
        $ctrl.regexEmail = /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/i;
        $ctrl.regexUsername = /^[a-z0-9]{5,20}$/;

        PartiturasServices.elementFocus('search');
        $ctrl.getUsers()
    };

    $ctrl.getUsers = () => {
        let url = Routing.generate('user_list_tbl');

        $ctrl.isLoading = true;

        $ctrl.options.skip = $ctrl.currentPage;
        $ctrl.options.limit = $ctrl.numItems;

        $ctrl.data = {
            'search': $ctrl.search,
            'options': $ctrl.options
        }

        $http.get(url, {params: $ctrl.data}).then(({data}) => {
            $ctrl.users = data.data;
            $ctrl.totalItems = data.count;
            $ctrl.isLoading = false;
        });

        $ctrl.openMdlRegisterUser = () => {
            const mdlRegisteUser = angular.element('#mdl-register-user');

            $ctrl.showPassword = {
                password: false,
                passwordConfirm: false,
            };

            $ctrl.resetFormState();

            mdlRegisteUser.modal('show');

            mdlRegisteUser.off('shown.bs.modal');

            mdlRegisteUser.on('shown.bs.modal', () => {
                $scope.formRegisterUser.$setPristine();
                $scope.formRegisterUser.$setUntouched();
                PartiturasServices.elementFocus('user_name');
                form = $scope.formRegisterUser;
            })
        }

        $ctrl.registerUser = ($event) => {
            $event.preventDefault();
            if (form.$valid && $ctrl.validPassword && $ctrl.validEmail && $ctrl.validUsername && $ctrl.samePasswords) {
                $ctrl.saveUser();
            } else {
                let fieldsUserForm = ['user[name]', 'user[email]', 'user[username]', 'user[password]', 'user[password_confirm]'];

                $ctrl.campoError = PartiturasServices.validarCamposFormService(form, fieldsUserForm);

                if ($ctrl.campoError) {
                    let fieldMap = [
                        ['nameUserError', 'user[name]'],
                        ['emailError', 'user[email]'],
                        ['usernameError', 'user[username]'],
                        ['passwordError', 'user[password]'],
                        ['passwordConfirmError', 'user[password_confirm]']
                    ];

                    $ctrl.validateFields(fieldMap);
                    PartiturasServices.elementFocus($ctrl.campoError.$$attr.id)
                } else {
                    if (!$ctrl.validEmail) {
                        PartiturasServices.elementFocus('user_email');
                    } else if (!$ctrl.validUsername) {
                        PartiturasServices.elementFocus('user_username');
                    } else {
                        let idInput = !$ctrl.validPassword ? 'user_password' : 'user_password_confirm';
                        PartiturasServices.elementFocus(idInput);

                        if (idInput === 'user_password_confirm')
                            $ctrl.passwordConfirmError = true;

                    }
                }
            }
        }

        $ctrl.togglePassword = (field) => {
            $ctrl.showPassword[field] = !$ctrl.showPassword[field];
        };

        $ctrl.validatePassword = () => {
            $ctrl.validPassword = $ctrl.regexPassword.test($ctrl.password);
        }

        $ctrl.validateEmail = () => {
            $ctrl.validEmail = $ctrl.regexEmail.test($ctrl.email);
        }

        $ctrl.validateUsername = () => {
            $ctrl.validUsername = $ctrl.regexUsername.test($ctrl.username);
        }

        $ctrl.validateSamePasswords = () => {
            $ctrl.samePasswords = $ctrl.password === $ctrl.passwordConfirm;
        }

        $ctrl.validateFieldsForm = (prop, field) => {
            $ctrl[prop] = PartiturasServices.hasErrorService(form, field, $ctrl.campoError?.$name);
            if (prop === 'passwordConfirmError')
                $ctrl.passwordConfirmError = $ctrl.passwordConfirm !== $ctrl.password;
        }

        $ctrl.resetFormState = () => {
            $ctrl.nameUser = $ctrl.email = $ctrl.username = $ctrl.password = $ctrl.passwordConfirm = '';
            $ctrl.validUsername = $ctrl.validEmail = $ctrl.validPassword = true;
            $ctrl.nameUserError = $ctrl.emailError = $ctrl.usernameError = $ctrl.passwordError = $ctrl.passwordConfirmError = false;
        };

        $ctrl.saveUser = (user = null) => {
            $ctrl.saveUserLoading = true;

            let url = Routing.generate('user_register');

            let data = {
                'name': $ctrl.nameUser.toUpperCase(),
                'email': $ctrl.email,
                'username': $ctrl.username,
                'password': $ctrl.password,
                'id': user?.id ?? null,
            }

            $http.post(url, data).then(({data}) => {
                $ctrl.saveUserLoading = false;
                PartiturasServices.toasterMessageService(data.message, data.status)

                if (data.status === 'error') {
                    $ctrl.roleError = true;
                    PartiturasServices.elementFocus('user_name');
                } else {
                    angular.element('#mdl-register-user').modal('hide');
                    $ctrl.getUsers();
                }
            })
        }

        $ctrl.openMdlDeleteUser = (user) => {
            $ctrl.username = user.username;
            $ctrl.email = user.email;
            $ctrl.nameUser = user.name;
            $ctrl.idUserDelete = user.id;
            angular.element('#mdl-delete-user').modal('show');
        }

        $ctrl.deleteUser = function () {
            $ctrl.isLoadingModalDeleteUser = true;
            let url = Routing.generate('user_delete');

            $http.delete(url, {data: {id: $ctrl.idUserDelete}}).then(({data}) => {
                $ctrl.isLoadingModalDeleteUser = false;

                angular.element('#mdl-delete-user').modal('hide');
                PartiturasServices.toasterMessageService(data.message, data.status);
                $ctrl.getUsers();
            });
        }

        $ctrl.openMdlEditUser = (user) => {
            $ctrl.groups = [];
            $ctrl.editPassword = false;
            $ctrl.isLoadingGroups = true;

            const url = Routing.generate('security_list_groups_tbl')
            const mdlEditUser = angular.element('#mdl-edit-user');

            $http.get(url).then(({data}) => {
                $ctrl.groups = data
            })

            mdlEditUser.modal('show');

            $ctrl.userEdit.name = user.name;

            mdlEditUser.off('shown.bs.modal');

            mdlEditUser.on('shown.bs.modal', () => {
                PartiturasServices.elementFocus('user_edit_name');
            });
        }

        $ctrl.submitFormEdit = ($event) => {
            $event.preventDefault();
            console.log('editar')
        }

        $ctrl.focusMdlEditUser = () => {
            $timeout(() => {
                PartiturasServices.elementFocus($ctrl.editPassword ? 'user_edit_password' : 'user_edit_name');
            })
        }

        $ctrl.validateFields = (fieldMap) => {
            fieldMap.forEach(([prop, fieldName]) => {
                $ctrl[prop] = PartiturasServices.hasErrorService(form, fieldName, $ctrl.campoError.$name);
            });
        };
    }
}
export default function MenuController() {
    let $ctrl = this;

    $ctrl.isMenuOpen = function (routeName) {

        if (window.SYMFONY_ROUTE.startsWith('security')) {
            angular.element($('#item-user-settings')).addClass('menu-open');
        }

        return window.SYMFONY_ROUTE === routeName ? 'active' : '';
    }
}
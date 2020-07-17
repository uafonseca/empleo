const routes = require("../../public/bundles/fosjsrouting/js/fos_js_routing");

const Routing = require("../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min");

Routing.setRoutingData(routes);

module.exports = Routing;
require('./ajaxRender');
require('./clickhandler');
require('./imgur');
require('./more');
require('./reddit');
require('./scrollListener');
require('./twitter');
require('./viewedDb');
require('./youtube');
require('../css/app.scss');

import loadMoreLolz from "./more";
import ajaxRender from "./ajaxRender";

window.scrollTo(0, 0);

loadMoreLolz().then(() => {
    ajaxRender();
});
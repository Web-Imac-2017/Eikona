var loc = window.location.pathname;
var dir = loc.substring(0, loc.lastIndexOf('/'));
var apiRoot = dir + '/do/'
export default apiRoot

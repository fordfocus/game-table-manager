'use strict';

String.prototype.format = function() {
    return [...arguments].reduce((pattern,value) => pattern.replace(/%s/,value), this);
};
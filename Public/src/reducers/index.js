var combineReducers = require('redux').combineReducers;
var moments = require('./moments');

var rootReducer = combineReducers({
    moments
});

module.exports = rootReducer;

var types = require('../constants/ActionTypes');

var initialState = [];

function moments(state, action) {
    if (state == undefined) state = initialState;
    switch (action.type) {
        case types.ADD_MOMENT:
            return [action.moment].concat(state);
        default:
            return state;
    }
}

module.exports = moments;

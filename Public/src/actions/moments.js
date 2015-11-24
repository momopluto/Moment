var types = require('../constants/ActionTypes');

function addMoment (moment) {
    return {
        type: types.ADD_MOMENT,
        moment: moment
    };
}

exports.addMoment = addMoment;

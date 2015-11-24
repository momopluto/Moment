var React = require('react');
var PropTypes = React.PropTypes;
var bindActionCreators = require('redux').bindActionCreators;
var connect = require('react-redux').connect;
var MomentActions = require('../actions/moments');
var Publisher = require('../components/Publisher');
var MomentList = require('../components/MomentList');

var App = React.createClass({
    propTypes: {
        moments: PropTypes.array.isRequired,
        actions: PropTypes.object.isRequired
    },
    render: function() {
        var moments = this.props.moments,
            actions = this.props.actions;
        return (
            <div>
                <Publisher addMoment={actions.addMoment} />
                <MomentList moments={moments} />
            </div>
        );
    }
});

function mapStateToProps (state) {
    return {
        moments: state.moments
    };
}

function mapDispatchToProps (dispatch) {
    return {
        actions: bindActionCreators(MomentActions, dispatch)
    };
}

module.exports = connect(
    mapStateToProps,
    mapDispatchToProps
)(App);

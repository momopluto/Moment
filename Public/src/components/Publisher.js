var React = require('react');
var assign = require('object-assign');

var Publisher = React.createClass({
    porpTypes: {
        addMoment: React.PropTypes.func.isRequired
    },
    getInitialState: function() {
        return {
            text: '',
            picFiles: [],  // 图片file对象
            pics: [],  // 图片对应的base64编码
            isPublished: true,
            leftText: 300,
            leftPic: 6
        };
    },
    render: function() {
        var pics = this.state.pics.map(function(item, index) {
            return (
                <div className="publish_pic" key={index} data-id={index} style={{backgroundImage:'url(' + item + ')'}}>
                    <a className="publish_delete icon-delete iconfont" onClick={this.deletePicHandler} href="javascript:;">&#xe603;</a>
                </div>
            );
        }.bind(this));
        if (this.state.leftPic > 0) {
            pics.push(
                <a className="add_pic" key="-1" onClick={this.clickHandler} href="javascript:;"></a>
            );
        }

        return (
            <div className="publisher_top">
                <div className="send_content">
                    <div className="input_area">
                        <textarea className="inputs" placeholder="此刻，你在想些什么~" rows="4" onInput={this.inputHandler} value={this.state.text}></textarea>
                    </div>
                    <div className="func_area clearfix">
                        <div className="kinds">
                            {pics}
                            <input className="uploader" ref="uploader" onChange={this.changeHandler} type="file" accept="image/jpeg" multiple />
                        </div>
                        <div className="func">
                            <p className="word_limit">还可以输入<span className="word_limit_num">{this.state.leftText}</span>字，上传<span className="word_limit_num">{this.state.leftPic}</span>张图片</p>
                            <div className="limits">
                                <a href="javascript:;" className="limits_btn">
                                    <span className="limits_txt">{this.state.isPublished ? '公开' : '不公开'}</span>
                                    <em className="arrow"></em>
                                    <ul className="limits_list">
                                        <li className="limits_item" onClick={this.selectHandler}>公开</li>
                                        <li className="limits_item" onClick={this.selectHandler}>不公开</li>
                                    </ul>
                                </a>
                            </div>
                            <a href="javascript:;" className="publish_btn" onClick={this.saveHandler} title="发布">Mo</a>
                        </div>
                    </div>
                </div>
            </div>
        );
    },
    inputHandler: function(e) {
        var text = e.srcElement || e.target;
        if (text.value.length > 300) {
            text.value = text.value.slice(300);
        }
        this.setState(assign({}, this.state, {
            text: text.value,
            leftText: 300 - text.value.length
        }));
    },
    clickHandler: function(e) {
        this.refs.uploader.click();
    },
    changeHandler: function(e) {
        var target = e.srcElement || e.target;
        var picFiles = Array.prototype.slice.call(target.files, 0);
        var pics = [];

        var readpics = function(i) {
            if (i == picFiles.length) {
                this.setState(assign({}, this.state, {
                    picFiles: this.state.picFiles.concat(picFiles).slice(0, 6),
                    pics: this.state.pics.concat(pics).slice(0, 6),
                    leftPic: 6 - this.state.pics.concat(pics).slice(0, 6).length
                }));
            } else {
                var reader = new FileReader();
                reader.readAsDataURL(picFiles[i]);
                reader.onload = function(e) {
                    pics.push(e.target.result);
                    readpics(++i);
                }
            }
        }.bind(this);
        readpics(0);
    },
    selectHandler: function(e) {
        var target = e.srcElement || e.target;
        if (target.innerHTML == '公开') {
            this.setState(assign({}, this.state, {
                isPublished: true
            }));
        } else {
            this.setState(assign({}, this.state, {
                isPublished: false
            }));
        }
    },

    /**
     * 删除当前图片
     */
    deletePicHandler: function(e) {
        var target = e.srcElement || e.target;
        var key = target.parentNode.getAttribute('data-id');
        this.setState(assign({}, this.state, {
            picFiles: this.state.picFiles.slice(0, key).concat(this.state.picFiles.slice(key + 1)),
            pics: this.state.pics.slice(0, key).concat(this.state.pics.slice(key + 1)),
            leftPic: ++this.state.leftPic
        }));
    },

    /**
     * 发布Moment
     */
    saveHandler: function() {
        if (this.state.picFiles.length == 0 && this.state.text == '') {
            alert('分享的内容不能为空');
            return;
        }
        this.props.addMoment({
            text: this.state.text,
            pics: this.state.pics,
            isPublished: this.state.isPublished,
            createAt: new Date().getTime()
        });
        this.setState({
            text: '',
            picFiles: [],  // 图片file对象
            pics: [],  // 图片对应的base64编码
            isPublished: true,
            leftText: 300,
            leftPic: 6
        });
    }
});

module.exports = Publisher;

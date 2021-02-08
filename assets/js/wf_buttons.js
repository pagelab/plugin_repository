(function () {
    const formatNameButton = 'wf_post_btn';
    const formatNameHighlight = 'wf_post_highlight';

    const init = () => {
        tinymce.PluginManager.add('wf_buttons', function (editor, url) {

            editor.addButton('wf_btn', {
                title: 'Button',
                image: homeUrl() + '/wp-content/plugins/wf-tinymce-buttons/assets/img/button-icon.png',
                onclick: function () {
                    editor.formatter.toggle(formatNameButton);
                },
                onPostRender: function() {
                    let _this = this;
                    editor.on('NodeChange', function(e) {
                        _this.active(editor.formatter.match(formatNameButton));
                    });
                }
            });

            editor.addButton('wf_highlight', {
                title: 'Highlight',
                image: homeUrl() + '/wp-content/plugins/wf-tinymce-buttons/assets/img/highlight-icon.png',
                onclick: function () {
                    editor.formatter.toggle(formatNameHighlight);
                },
                onPostRender: function() {
                    let _this = this;
                    editor.on('NodeChange', function(e) {
                        _this.active(editor.formatter.match(formatNameHighlight));
                    });
                }
            });

            editor.on('init', function (e) {
                editor.formatter.register(
                    formatNameButton, {
                        inline: 'span',
                        classes: ['wf-post-btn'],
                        wrapper: true
                    }
                );
                
                editor.formatter.register(
                    formatNameHighlight, {
                        block: 'div',
                        classes: ['wf-post-highlight'],
                        wrapper: true,
                    }
                );
            });

        });
        
    }

    const homeUrl = () => {
        const href = window.location.href;
        const index = href.indexOf('/wp-admin');
    
        return href.substring(0, index);
    }

    init();
})();

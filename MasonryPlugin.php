<?php

namespace RJPlugins;

use Gdn;
use Gdn_Plugin;
use Gdn_Format;

class MasonryPlugin extends Gdn_Plugin {
    /**
     *  Run on startup to init sane config settings and db changes.
     *
     *  @return void.
     */
    public function setup() {
        $this->structure();
    }

    /**
     *  Create tables and/or new columns.
     *
     *  @return void.
     */
    public function structure() {
        Gdn::config()->saveToConfig('Vanilla.Discussions.Layout', 'modern');
    }

    /**
     * Add CSS file.
     *
     * @param AssetModel $sender Instance of the calling class.
     *
     * @return void.
     */
    public function assetModel_styleCss_handler($sender) {
        $sender->addCssFile('masonry.css', 'plugins/masonry');
    }

    /**
     * Insert Discussion body into /discussions/xyz views.
     *
     * @param DiscussionsController $sender Instance of the calling class.
     * @param mixed $args Event arguments.
     *
     * @return void.
     */
    public function discussionsController_afterDiscussionContent_handler($sender, $args) {
        echo $this->getDiscussionContent($args['Discussion']);
    }

    /**
     * Insert Discussion body into /categories/xyz views.
     *
     * @param CategoriesController $sender Instance of the calling class.
     * @param mixed $args Event arguments.
     *
     * @return void.
     */
    public function categoriesController_afterDiscussionContent_handler($sender, $args) {
        echo $this->getDiscussionContent($args['Discussion']);
    }

    /**
     * Get formatted discussion body.
     *
     * @param object $discussion The discussion to format.
     *
     * @return string The formatted body text.
     */
    protected function getDiscussionContent($discussion) {
        $commentsLink = '';
        if ($discussion->FirstCommentID) {
            $commentID = $discussion->FirstCommentID;
            $link = "/discussion/comment/{$commentID}#Comment_{$commentID}";
            $commentsLink = '<div>'.
                anchor('Comments', $link, 'Button masonryCommentsLink').
                '</div>';
        }

        $result = '<div class="Message userContent masonry" id="masonry_'.$discussion->DiscussionID.'">';
        $result .= Gdn_Format::to($discussion->Body, $discussion->Format);
        $result .= '</div>';
        $result .= wrapIf($commentsLink, 'div', ' class="Buttons masonryButtons"');

        return $result;
    }
}

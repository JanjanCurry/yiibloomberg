<?php
$cats = DbVideoCat::model()->findAll(['order' => 'orderId ASC, name ASC']);
?>

<div class="content-header wow slideInDown">
    <div class="container text-center">
        <h3>Explainer Videos – Platform How To and Use Cases</h3>
    </div>
</div>

<div class="container page-how-to padding-top-30">
    <?php foreach ($cats as $cat) { ?>
        <?php if (!empty($cat->videos)) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="collapsed" data-toggle="collapse" data-target="#videos-<?php echo $cat->ref; ?>">
                        <span class="caret-icon"></span>
                        <?php echo $cat->name; ?>
                    </h4>
                </div>
                <div class="panel-body collapse" id="videos-<?php echo $cat->ref; ?>">
                    <div class="row-flex">
                        <div class="row-flex-wrap flex-grow">
                            <?php foreach ($cat->videos as $video) { ?>
                                <div class="col-sm-4 howto-video">
                                    <div class="flex-video">
                                        <iframe src="https://www.youtube.com/embed/<?php echo $video->video; ?>?rel=0&amp;showinfo=0"
                                                frameborder="0"
                                                allow="autoplay; encrypted-media"
                                                allowfullscreen
                                                id="howto-video-player"></iframe>
                                    </div>
                                    <h4><?php echo $video->title; ?></h4>
                                    <p><?php echo $video->description; ?></p>

                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</div>

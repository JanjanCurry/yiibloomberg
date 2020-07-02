<?php $this->registerFile('js','common/chartShare'); ?>

<div class="modal fade-scale" id="chartShareModal" tabindex="-1" role="dialog" aria-labelledby="chartShareModal">
    <div class="modal-dialog modal-fw" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>

                <div class="form-group">
                    <label class="control-label">Chart Image Link</label>
                    <span class="input-group">
                        <input type="text" readonly="readonly" class="form-control chart-img-link" value="" id="chartShareLink" />
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-primary btn-copy" data-target="#chartShareLink" aria-label="Copy"><span class="fa fa-copy"></span> Copy</button>
                        </span>
                    </span>
                </div>

                <div class="form-group">
                    <label class="control-label">Chart Embed</label>
                    <span class="input-group">
                        <input type="text" readonly="readonly" class="form-control chart-img-embed" value="" id="chartShareEmbed" />
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-primary btn-copy" data-target="#chartShareEmbed" aria-label="Copy"><span class="fa fa-copy"></span> Copy</button>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
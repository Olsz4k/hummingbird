{*
* Justidea WebP Converter - Stats Template
*
* @author Justidea Agency
* @copyright 2025 Justidea Agency
* @license AFL-3.0
*}

<div class="panel">
    <div class="panel-heading">
        <i class="icon-bar-chart"></i> {l s='WebP Conversion Statistics' mod='justidea_webpconverter'}
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-6">
                <div class="alert alert-info">
                    <h4><i class="icon-picture-o"></i> {l s='Total Images Converted' mod='justidea_webpconverter'}</h4>
                    <p class="text-center" style="font-size: 3em; margin: 10px 0;">
                        <strong>{$total_converted|intval}</strong>
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="alert alert-success">
                    <h4><i class="icon-hdd-o"></i> {l s='Total Space Saved' mod='justidea_webpconverter'}</h4>
                    <p class="text-center" style="font-size: 3em; margin: 10px 0;">
                        <strong>{$total_saved_mb|string_format:"%.2f"} MB</strong>
                    </p>
                </div>
            </div>
        </div>

        <div class="alert alert-warning">
            <h4><i class="icon-info-circle"></i> {l s='System Information' mod='justidea_webpconverter'}</h4>
            <ul>
                <li>
                    <strong>{l s='ImageMagick:' mod='justidea_webpconverter'}</strong>
                    {if extension_loaded('imagick')}
                        <span class="badge badge-success">{l s='Available' mod='justidea_webpconverter'}</span>
                    {else}
                        <span class="badge badge-danger">{l s='Not Available' mod='justidea_webpconverter'}</span>
                    {/if}
                </li>
                <li>
                    <strong>{l s='GD Library (WebP):' mod='justidea_webpconverter'}</strong>
                    {if function_exists('imagewebp')}
                        <span class="badge badge-success">{l s='Available' mod='justidea_webpconverter'}</span>
                    {else}
                        <span class="badge badge-danger">{l s='Not Available' mod='justidea_webpconverter'}</span>
                    {/if}
                </li>
            </ul>
        </div>
    </div>
</div>

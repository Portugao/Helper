{* Purpose of this template: Display a popup selector for Forms and Content integration *}
{assign var='baseID' value='productClass'}
<div id="itemSelectorInfo" class="hidden" data-base-id="{$baseID}" data-selected-id="{$selectedId|default:0}"></div>
<div class="row">
    <div class="col-sm-8">
        <div class="form-group">
            <label for="{$baseID}Id" class="col-sm-3 control-label">{gt text='Product class'}:</label>
            <div class="col-sm-9">
                <select id="{$baseID}Id" name="id" class="form-control">
                    {foreach item='productClass' from=$items}
                        <option value="{$productClass->getKey()}"{if $selectedId eq $productClass->getKey()} selected="selected"{/if}>{$productClass->getTitle()}</option>
                    {foreachelse}
                        <option value="0">{gt text='No entries found.'}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="{$baseID}Sort" class="col-sm-3 control-label">{gt text='Sort by'}:</label>
            <div class="col-sm-9">
                <select id="{$baseID}Sort" name="sort" class="form-control">
                    <option value="title"{if $sort eq 'title'} selected="selected"{/if}>{gt text='Title'}</option>
                    <option value="description"{if $sort eq 'description'} selected="selected"{/if}>{gt text='Description'}</option>
                    <option value="getAmazonDatas"{if $sort eq 'getAmazonDatas'} selected="selected"{/if}>{gt text='Get amazon datas'}</option>
                    <option value="operation"{if $sort eq 'operation'} selected="selected"{/if}>{gt text='Operation'}</option>
                    <option value="responseGroup"{if $sort eq 'responseGroup'} selected="selected"{/if}>{gt text='Response group'}</option>
                    <option value="searchIndex"{if $sort eq 'searchIndex'} selected="selected"{/if}>{gt text='Search index'}</option>
                    <option value="keywordsForImport"{if $sort eq 'keywordsForImport'} selected="selected"{/if}>{gt text='Keywords for import'}</option>
                    <option value="sortOfImport"{if $sort eq 'sortOfImport'} selected="selected"{/if}>{gt text='Sort of import'}</option>
                    <option value="additionalFilter"{if $sort eq 'additionalFilter'} selected="selected"{/if}>{gt text='Additional filter'}</option>
                    <option value="includeReviewSummary"{if $sort eq 'includeReviewSummary'} selected="selected"{/if}>{gt text='Include review summary'}</option>
                    <option value="itemPageForImport"{if $sort eq 'itemPageForImport'} selected="selected"{/if}>{gt text='Item page for import'}</option>
                    <option value="associateKeyOfAmazon"{if $sort eq 'associateKeyOfAmazon'} selected="selected"{/if}>{gt text='Associate key of amazon'}</option>
                </select>
                <select id="{$baseID}SortDir" name="sortdir" class="form-control">
                    <option value="asc"{if $sortdir eq 'asc'} selected="selected"{/if}>{gt text='ascending'}</option>
                    <option value="desc"{if $sortdir eq 'desc'} selected="selected"{/if}>{gt text='descending'}</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="{$baseID}SearchTerm" class="col-sm-3 control-label">{gt text='Search for'}:</label>
            <div class="col-sm-9">
                <div class="input-group">
                    <input type="text" id="{$baseID}SearchTerm" name="q" class="form-control" />
                    <span class="input-group-btn">
                        <input type="button" id="mUHelperModuleSearchGo" name="gosearch" value="{gt text='Filter'}" class="btn btn-default" />
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div id="{$baseID}Preview" style="border: 1px dotted #a3a3a3; padding: .2em .5em">
            <p><strong>{gt text='Product class information'}</strong></p>
            {img id='ajaxIndicator' modname='core' set='ajax' src='indicator_circle.gif' alt='' class='hidden'}
            <div id="{$baseID}PreviewContainer">&nbsp;</div>
        </div>
    </div>
</div>

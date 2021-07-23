import './editor.scss';
import { __ } from './../i18n/i18n.js';
import {
	RichText,
	MediaUpload,
	BlockControls,
	AlignmentToolbar,
	RichTextToolbarButton,
	InspectorControls,
	PanelColorSettings
} from "@wordpress/block-editor";
import {
	ColorPalette,
	TextControl,
	ToggleControl,
	Panel, PanelBody, PanelRow
} from '@wordpress/components';
import { isWhitespaceCharacter } from 'is-whitespace-character'

const { registerBlockType } = wp.blocks;

const getStyleClass = (className = "") => {
	if (className.includes("mini")){
		return "github-info-card-mini";
	}
	return "github-info-card-full";
}

registerBlockType('argon/github', {
	title: __('Github Repo 信息卡'),
	icon: 'github fa fa-github',
	category: 'argon',
	keywords: [
		'argon',
		'github',
		__('Github Repo 信息卡')
	],
	styles: [
		{
			name: 'github-info-card-full',
			label: 'Full',
			isDefault: true
		},{
			name: 'github-info-card-mini',
			label: 'Mini',
		},
	],
	attributes: {
		author: {
			type: 'string',
			default: ''
		},
		project: {
			type: 'string',
			default: ''
		}
	},
	edit: (props) => {
		const onChangeAuthor = (value) => {
			props.setAttributes({ author: value });
		};
		const onChangeProject = (value) => {
			props.setAttributes({ project: value });
		};

		return (
			<div>
				<div className={`github-info-card card shadow-sm ${getStyleClass(props.className)}`}>
					<div className="github-info-card-header">
						<a title="Github">
							<span><i className="fa fa-github"></i> GitHub</span>
						</a>
					</div>
					<div className="github-info-card-body">
						<div className="github-info-card-name-a">
							<span>
								<span className="github-info-card-name">
									<span contentEditable data-placeholder="user" onBlur={e => onChangeAuthor(e.currentTarget.textContent)}>{props.attributes.author}</span>
									&nbsp;/&nbsp;
									<span contentEditable data-placeholder="project" onBlur={e => onChangeProject(e.currentTarget.textContent)}>{props.attributes.project}</span>
								</span>
							</span>
						</div>
						<div className="github-info-card-description">{__("Repo 描述将会显示在这里")}</div>
					</div>
					<div className="github-info-card-bottom">
						<span className="github-info-card-meta github-info-card-meta-stars">
							<i className="fa fa-star"></i>&nbsp;<span className="github-info-card-stars">0</span>
						</span>
						<span className="github-info-card-meta github-info-card-meta-forks">
							<i className="fa fa-code-fork"></i>&nbsp;<span className="github-info-card-forks">0</span>
						</span>
					</div>
				</div>
				<InspectorControls key="setting">
					<PanelBody title={__("区块设置")} icon={"more"} initialOpen={true}>
						<PanelRow>
							<div id="gutenpride-controls">
							<fieldset>
									<PanelRow>Repo</PanelRow>
									<TextControl
										label="User"
										value={props.attributes.author}
										onChange={onChangeAuthor}
									/>
									<TextControl
										label="Project"
										value={props.attributes.project}
										onChange={onChangeProject}
									/>
								</fieldset>
							</div>
						</PanelRow>
					</PanelBody>
				</InspectorControls>
			</div>
		);
	},
	save: (props) => {
		return (
			<div className={`github-info-card card shadow-sm ${getStyleClass(props.className || props.attributes.className)}`} data-author={props.attributes.author} data-project={props.attributes.project}>
				<div className="github-info-card-header">
					<a href="https://github.com/" ref="nofollow" target="_blank" title="Github" rel="noopener">
						<span><i className="fa fa-github"></i>&nbsp;GitHub</span>
					</a>
				</div>
				<div className="github-info-card-body">
					<div className="github-info-card-name-a">
						<a href={`https://github.com/${props.attributes.author}/${props.attributes.project}`} target="_blank" rel="noopener">
							<span className="github-info-card-name">{props.attributes.author}/{props.attributes.project}</span>
						</a>
					</div>
					<div className="github-info-card-description"></div>
				</div>
				<div className="github-info-card-bottom">
					<span className="github-info-card-meta github-info-card-meta-stars">
						<i className="fa fa-star"></i>&nbsp;<span className="github-info-card-stars"></span>
					</span>
					<span className="github-info-card-meta github-info-card-meta-forks">
						<i className="fa fa-code-fork"></i>&nbsp;<span className="github-info-card-forks"></span>
					</span>
				</div>
			</div>
		);
	},
});
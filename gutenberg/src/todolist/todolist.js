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
	Button,
	Panel, PanelBody, PanelRow
} from '@wordpress/components';

const { registerBlockType } = wp.blocks;

registerBlockType('argon/todolist', {
	title: 'Todo List',
	icon: 'yes-alt',
	category: 'argon',
	keywords: [
		'argon',
		'Todo list'
	],
	attributes: {
		list: {
			type: 'array',
			default: [{
				content: '',
				checked: false,
				id: 0,
				deleted: false
			}]
		}
	},
	edit: (props) => {
		const addNode = () => {
			let newList = [
				...props.attributes.list,
				{
					content: '',
					checked: false,
					id: props.attributes.list.length,
					deleted: false
				}
			]
			props.setAttributes({ list: newList });
		};
		const removeNode = (id) => {
			let newList = props.attributes.list.map((node) => {
				if (node.id == id){
					return {...node, deleted: true}
				}
				return node;
			});
			props.setAttributes({ list: newList });
		}
		const onChangeContent = (value, id) => {
			let newList = props.attributes.list.map((node) => {
				if (node.id == id){
					return {...node, content: value}
				}
				return node;
			});
			props.setAttributes({ list: newList });
		};
		const onChangeCheckStatus = (value, id) => {
			console.log(value);
			let newList = props.attributes.list.map((node) => {
				if (node.id == id){
					return {...node, checked: value}
				}
				return node;
			});
			props.setAttributes({ list: newList });
		};

		const nodelist = props.attributes.list.map((node) => {
			if (node.deleted == true){
				return;
			}
			return (
				<div class="shortcode-todo custom-control custom-checkbox" key={node.id.toString()}>
					<input class="custom-control-input" type="checkbox" checked={node.checked}/>
					<label class="custom-control-label" onClick={e => onChangeCheckStatus(!node.checked, node.id)}/>
					<RichText
						tagName="span"
						placeholder={__("条目内容")}
						value={node.content}
						onChange={value => onChangeContent(value, node.id)}
					/>
					<Button className="is-tertiary todolist-remove-item-btn" onClick={() => removeNode(node.id)} style={{color: "#7889e8"}}><span class="dashicon dashicons dashicons-trash"></span></Button>
				</div>
			);
		});

		return (
			<div>
				{nodelist}
				<Button className="is-primary" onClick={addNode} style={{marginTop: 8, backgroundColor: "#7889e8"}}>{__("+ 添加条目")}</Button>
			</div>
		);
	},
	save: (props) => {
		const nodelist = props.attributes.list.map((node) => {
			if (node.deleted == true){
				return;
			}
			return (
				<div class="shortcode-todo custom-control custom-checkbox">
					<input class="custom-control-input" type="checkbox" checked={node.checked ? true : null}/>
					<label class="custom-control-label">
						<span dangerouslySetInnerHTML={{ __html: node.content }}/>
					</label>
				</div>
			);
		});
		
		return (
			<div style="margin-bottom: 20px;">
				{nodelist}
			</div>
		);
	},
});
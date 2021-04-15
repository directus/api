<template>
	<div class="install-requirements">
		<template v-if="fetchingRequirements">
			<v-spinner class="spinner" color="#263238" />
		</template>
		<template v-else-if="error">
			<v-notice color="danger" icon="error">
				{{ error }}
			</v-notice>
		</template>
		<template v-else>
			<v-notice
				v-for="cat in [server, phpVersion, phpExtensions, permissions, directusVersion]"
				:key="cat.key"
				:color="cat.success ? 'success' : 'warning'"
				:icon="cat.success ? 'check' : 'warning'"
			>
				<div class="content">
					{{ cat.value }}
					<a
						v-if="cat.success === false"
						href="https://docs.directus.io/installation/git.html#step-1-make-sure-requirements-are-met"
						target="__blank"
					>
						{{ $t('why') }}
					</a>
				</div>
			</v-notice>
		</template>
	</div>
</template>

<script>
import { mapState } from 'vuex';
import axios from 'axios';
import { satisfies } from 'semver';

export default {
	name: 'InstallRequirements',
	props: {
		superAdminToken: {
			type: String,
			required: false,
			default: null
		}
	},
	data() {
		return {
			fetchingRequirements: false,
			serverInfo: null,
			error: null,
			lastTag: null
		};
	},
	computed: {
		...mapState(['apiRootPath']),
		server() {
			if (this.fetchingRequirements || this.serverInfo === null) return;

			return {
				key: 'server',
				success: this.serverInfo.server.type.toLowerCase().includes('apache'),
				value: this.serverInfo.server.type
			};
		},
		phpVersion() {
			if (this.fetchingRequirements || this.serverInfo === null) return;
			const version = this.serverInfo.php.version.split('-')[0];
			const minimumVersion = '7.1.0';

			return {
				key: 'phpVersion',
				success: satisfies(version, `>=${minimumVersion}`),
				value: `PHP ${version}`
			};
		},
		phpExtensions() {
			if (this.fetchingRequirements || this.serverInfo === null) return;
			const extensions = Object.keys(this.serverInfo.php.extensions).map(key => ({
				key,
				enabled: this.serverInfo.php.extensions[key]
			}));

			const allEnabled = extensions.every(e => e.enabled);

			let value = this.$t('php_extensions');

			if (allEnabled === false) {
				value +=
					': ' +
					this.$t('missing_value', {
						value: extensions.filter(e => e.enabled === false).map(e => e.key)
					});
			}

			return {
				key: 'phpExtensions',
				success: allEnabled,
				value: value
			};
		},
		permissions() {
			if (this.fetchingRequirements || this.serverInfo === null) return;
			const permissions = Object.keys(this.serverInfo.permissions).map(key => ({
				key,
				permission: this.serverInfo.permissions[key]
			}));

			const failedFolders = permissions.filter(p => +p.permission[1] !== 7);
			const success = failedFolders.length === 0;

			let value = this.$t('write_access');

			if (success === false) {
				value +=
					': ' +
					this.$t('value_not_writeable', {
						value: failedFolders.map(f => `/${f.key}`).join(', ')
					});
			}

			return {
				key: 'permissions',
				success: failedFolders.length === 0,
				value: value
			};
		},
		directusVersion() {
			if (this.fetchingRequirements || this.serverInfo === null || this.lastTag === null)
				return;

			return {
				key: 'directusVersion',
				success: 'v' + this.serverInfo.directus === this.lastTag,
				value: this.$t('directus_version') + ': v' + this.serverInfo.directus
			};
		}
	},
	created() {
		this.fetchRequirements();
	},
	methods: {
		async fetchRequirements() {
			this.fetchingRequirements = true;
			this.error = null;

			try {
				const serverInfoResponse = await axios.get(this.apiRootPath + 'server/info', {
					params: {
						super_admin_token: this.superAdminToken
					}
				});
				this.serverInfo = serverInfoResponse.data.data;
			} catch (error) {
				const code = error.response?.data?.error?.code;

				if (+code === 3) {
					this.error = this.$t('wrong_super_admin_password');
				} else {
					this.error = error.message;
				}
			}

			try {
				const githubVersionResponse = await axios.get(
					'https://api.github.com/repos/directus/v8-archive/tags'
				);
				this.lastTag = githubVersionResponse.data.find(
					tag => tag.name.includes('-') === false
				).name;
			} catch {
				console.log("Couldn't fetch latests version of Directus from GitHub");
			}

			this.fetchingRequirements = false;
		}
	}
};
</script>

<style lang="scss" scoped>
.spinner {
	width: 32px;
	margin-bottom: 32px;
}
.notice {
	height: 60px;
	margin-bottom: 8px;
	max-width: none !important;
}

.content {
	width: 100%;
	a {
		float: right;
	}
}

.install-requirements {
	margin-bottom: 32px;
}
</style>

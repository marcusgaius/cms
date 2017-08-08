export const cartPlugins = state => {
    return state.cart.items.map(({ id }) => {
        if(state.pluginstore.data.plugins) {
            return state.pluginstore.data.plugins.find(p => p.id === id)
        }
    })
}

export const activeTrialPlugins = state => {
    if(!state.craft.craftData.installedPlugins) {
        return [];
    }

    let plugins = state.craft.craftData.installedPlugins.map( id  => {
        if(state.pluginstore.data.plugins) {
            return state.pluginstore.data.plugins.find(p => p.id == id)
        }
    })

    return plugins.filter(p => {
        if(p) {
            return p.price > 0;
        }
    });
}

import Animation from './Animation.js';

export default class Queries extends Animation{  

    constructor(){
        super();
    }

    async axiosDelete(url, data = {}) {
        try {
            const response = await axios.delete(url, {
                headers: {
                    'Content-Type': 'application/json'
                },
                data: data,
            });
            return response;
        }catch(error){
            console.error('Error:', error);
            throw error;
        };
    }

    async axiosGet(url) {
        try {
            const response = await axios.get(url, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            
            return response.data;
        }catch(error){
            console.error('Error:', error);
            throw error;
        };
    }

    async axiosPut(url, headers = {}) {        
        try {
            const response = await axios.put(url, {
                ...headers.data
                }
            );
            
            return response.data;
        } catch(error){
            console.error('Error', error);
        }
    }

    async axiosPatch(url, data = {}) {
        try {
            const response = await axios.patch(url, {
                ...data
            });
            return response.data;
        } catch(error){
            console.error('Error:', error);
            throw error;
        };
    }

    async axiosPost(url, data = {}) {
        try {
            const response = await axios.post(url, {
                ...data
            });
            return response;
        }catch(error){
            console.error('Error:', error);
            throw error;
        };
    }

    async fetchWithToken(url, options = {}, callback = null ) {
        try {
            const response = await fetch(url, {
                ...options,
                headers: {
                    'Content-Type': 'application/json',
                    ...options.headers,
                },
                ...options.body
            });
    
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
    
            const result = await response.json();

            if (callback && typeof callback === 'function') {
                await callback(result);
            }

            return result;

        } catch (error) {
            console.error('Error:', error);
        }
    }

    postData(url = '', data = {}) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);

        for (const key in data) {
            if (data.hasOwnProperty(key)) {
                if(Array.isArray(data[key])) {
                    data[key].forEach(value => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `${key}[]`;
                        input.value = value;
                        form.appendChild(input);
                    });
                }else{
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = data[key];
                    form.appendChild(input);
                }
            }
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}
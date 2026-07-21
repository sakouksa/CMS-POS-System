import axios from "axios";
import config from "./config";
import {
  profileStore
} from "../store/profileStore";

export const request = (url = "", method = "", data = {}, option = {}) => {
  let access_token = profileStore.getState().access_token;
  let headers = {
    "Content-Type": "application/json", //json data
  };
  if (data instanceof FormData) {
    headers = {
      "Content-Type": "multipart/form-data", // form data
    };
  }

  return axios({
      url: config.base_url + url,
      method: method, //"get","post" ,"put","delete"
      data: data,
      responseType: option.responseType || "json",
      headers: {
        ...headers,
        Accept: "application/json",
        Authorization: "Bearer " + access_token,
      },
    })
    .then((res) => {
      return res.data;
    })
    .catch(async (error) => {
      const response = error.response;
      if (response) {
        let data = response.data;

        if (data instanceof Blob) {
          const text = await data.text();
          try {
            data = JSON.parse(text);
          } catch (e) {
            data = { message: text || "Unknown Error" };
          }
        }

        const status = response.status;
        let errors = {
          message: data?.message || "An error occurred",
        };

        if (status === 401) {
          profileStore.getState().logout();
          window.location.href = '/login';
          return {
            error: true,
            status: 401,
            errors: { message: "Session expired. Please log in again." }
          };
        }

        if (status === 500) {
          return {
            error: true,
            status: 500,
            message: data?.message || "500 : មានបញ្ហាបច្ចេកទេសក្នុងប្រព័ន្ធ!",
            errors: { message: data?.message || "500 : មានបញ្ហាបច្ចេកទេសក្នុងប្រព័ន្ធ!" }
          };
        }

        if (data.errors) {
          Object.keys(data.errors).forEach((key) => {
            errors[key] = {
              validateStatus: "error",
              help: data.errors[key][0],
              hasFeedback: true,
            };
          });
        }
        return {
          error: true,
          status: status,
          message: data?.message || "Validation Error",
          errors: errors,
        };
      }

      return {
        error: true,
        errors: {
          message: "501 : មិនអាចតភ្ជាប់ទៅកាន់ Server បានទេ!"
        },
      };
    });
};
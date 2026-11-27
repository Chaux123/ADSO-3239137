import { useEffect, useState } from 'react';
import { getToken, getToyCoverUrl } from '../services/api.js';

export default function ToyCover({ toy, onError, ...props }) {
  const [url, setUrl] = useState(null);

  useEffect(() => {
    const controller = new AbortController();
    let objectUrl;
    setUrl(null);
    fetch(getToyCoverUrl(toy.id), {
      headers: { Authorization: `Bearer ${getToken()}` },
      signal: controller.signal
    })
      .then((response) => {
        if (!response.ok) throw new Error('No se pudo cargar la imagen.');
        return response.blob();
      })
      .then((blob) => {
        if (!controller.signal.aborted) {
          objectUrl = URL.createObjectURL(blob);
          setUrl(objectUrl);
        }
      })
      .catch((error) => {
        if (error.name !== 'AbortError') onError?.();
      });
    return () => {
      controller.abort();
      if (objectUrl) URL.revokeObjectURL(objectUrl);
    };
  }, [toy.id, toy.cover_key]);

  return url ? <img src={url} onError={onError} {...props} /> : null;
}
